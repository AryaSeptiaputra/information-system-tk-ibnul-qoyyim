#!/usr/bin/env python3
"""Excel (.xlsx) vs DB schema audit helper.

Goals:
- Extract sheet names and likely header row + header cells from .xlsx files.
- Optionally extract an approximate DB schema (tables + columns) by scanning Laravel migrations.
- Produce a JSON report that can be used to score how well Excel templates match the current DB design.

This script intentionally uses only the Python standard library (no openpyxl dependency).

Usage examples:
  python scripts/excel_audit.py --excel-dir storage/app/excel_audit --out storage/app/excel_audit/report.json
  python scripts/excel_audit.py --excel-dir storage/app/excel_audit --out storage/app/excel_audit/report.json --migrations-dir database/migrations

Notes:
- Header-row detection is heuristic: the non-empty row with the most filled cells
  among the first N rows (default 20).
- Migration parsing is best-effort; it focuses on column *names*.
"""

from __future__ import annotations

import argparse
import datetime as _dt
import json
import os
import re
import sys
import zipfile
from dataclasses import dataclass
from typing import Dict, Iterable, List, Optional, Sequence, Tuple
from xml.etree import ElementTree as ET


NS = {
    "main": "http://schemas.openxmlformats.org/spreadsheetml/2006/main",
}

REL_NS_OFFICE = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
REL_NS_PACKAGE = "http://schemas.openxmlformats.org/package/2006/relationships"


def _norm(s: str) -> str:
    s = (s or "").strip().lower()
    # collapse whitespace
    s = re.sub(r"\s+", " ", s)
    # remove punctuation for matching
    s = re.sub(r"[^a-z0-9 ]+", "", s)
    s = s.replace(" ", "")
    return s


def _safe_read_zip_text(z: zipfile.ZipFile, path: str) -> Optional[str]:
    try:
        with z.open(path) as f:
            return f.read().decode("utf-8")
    except KeyError:
        return None


def _col_letter_to_index(col: str) -> int:
    # A -> 1, B -> 2 ... AA -> 27
    col = col.upper()
    idx = 0
    for ch in col:
        if not ("A" <= ch <= "Z"):
            break
        idx = idx * 26 + (ord(ch) - ord("A") + 1)
    return idx


def _cell_ref_to_col_index(cell_ref: str) -> int:
    # "C12" -> 3
    m = re.match(r"^([A-Za-z]+)", cell_ref or "")
    if not m:
        return 0
    return _col_letter_to_index(m.group(1))


def _parse_shared_strings(xml_text: Optional[str]) -> List[str]:
    if not xml_text:
        return []
    root = ET.fromstring(xml_text)
    out: List[str] = []
    for si in root.findall("main:si", NS):
        # shared strings can be <t> or multiple <r><t>
        parts: List[str] = []
        t = si.find("main:t", NS)
        if t is not None and t.text is not None:
            parts.append(t.text)
        else:
            for r in si.findall("main:r", NS):
                rt = r.find("main:t", NS)
                if rt is not None and rt.text is not None:
                    parts.append(rt.text)
        out.append("".join(parts))
    return out


@dataclass
class SheetInfo:
    name: str
    xml_path: str


def _parse_workbook_sheets(z: zipfile.ZipFile) -> List[SheetInfo]:
    workbook_xml = _safe_read_zip_text(z, "xl/workbook.xml")
    rels_xml = _safe_read_zip_text(z, "xl/_rels/workbook.xml.rels")

    if not workbook_xml or not rels_xml:
        return []

    wb = ET.fromstring(workbook_xml)
    rels = ET.fromstring(rels_xml)

    # workbook.xml.rels uses the *package* relationships namespace.
    # Use a namespace-agnostic lookup to be resilient.
    rid_to_target: Dict[str, str] = {}
    for rel in rels.findall(".//{*}Relationship"):
        rid = rel.attrib.get("Id")
        target = rel.attrib.get("Target")
        if not rid or not target:
            continue
        # Targets are relative to xl/
        if not target.startswith("/"):
            target = "xl/" + target.lstrip("/")
        rid_to_target[rid] = target

    sheets: List[SheetInfo] = []
    for s in wb.findall("main:sheets/main:sheet", NS):
        name = s.attrib.get("name", "")
        # In workbook.xml, r:id uses the *officeDocument* relationships namespace.
        rid = s.attrib.get(f"{{{REL_NS_OFFICE}}}id")
        if not rid:
            # Fallback: search any attribute that endswith '}id'
            for k, v in s.attrib.items():
                if k.endswith("}id") and v:
                    rid = v
                    break
        if not name or not rid:
            continue
        target = rid_to_target.get(rid)
        if not target:
            continue
        sheets.append(SheetInfo(name=name, xml_path=target))
    return sheets


def _cell_value(cell: ET.Element, shared_strings: Sequence[str]) -> str:
    """Return the display-ish value of a cell (best effort)."""
    t = cell.attrib.get("t")
    v = cell.find("main:v", NS)
    if t == "s":
        if v is None or v.text is None:
            return ""
        try:
            idx = int(v.text)
            return shared_strings[idx] if 0 <= idx < len(shared_strings) else ""
        except ValueError:
            return ""
    if t == "inlineStr":
        is_el = cell.find("main:is", NS)
        if is_el is None:
            return ""
        t_el = is_el.find("main:t", NS)
        if t_el is not None and t_el.text is not None:
            return t_el.text
        # rich text inline
        parts: List[str] = []
        for r in is_el.findall("main:r", NS):
            rt = r.find("main:t", NS)
            if rt is not None and rt.text is not None:
                parts.append(rt.text)
        return "".join(parts)

    # default: numeric/string in <v>
    if v is None or v.text is None:
        return ""
    return v.text


def _extract_sheet_rows(
    z: zipfile.ZipFile,
    sheet_xml_path: str,
    shared_strings: Sequence[str],
    max_rows: int,
) -> List[List[str]]:
    xml_text = _safe_read_zip_text(z, sheet_xml_path)
    if not xml_text:
        return []

    root = ET.fromstring(xml_text)
    sheet_data = root.find("main:sheetData", NS)
    if sheet_data is None:
        return []

    rows_out: List[List[str]] = []
    for row in sheet_data.findall("main:row", NS):
        r_attr = row.attrib.get("r")
        # Rows are 1-indexed in xlsx
        try:
            rnum = int(r_attr) if r_attr else None
        except ValueError:
            rnum = None

        # Stop after max_rows (heuristic; rows may skip numbers)
        if rnum is not None and rnum > max_rows:
            break

        cells = row.findall("main:c", NS)
        if not cells:
            rows_out.append([])
            continue

        # place values by column index to preserve gaps
        max_col = 0
        values_by_col: Dict[int, str] = {}
        for c in cells:
            ref = c.attrib.get("r", "")
            col_idx = _cell_ref_to_col_index(ref)
            if col_idx <= 0:
                continue
            max_col = max(max_col, col_idx)
            values_by_col[col_idx] = _cell_value(c, shared_strings).strip()

        row_vals = [""] * max_col
        for col_idx, val in values_by_col.items():
            row_vals[col_idx - 1] = val
        rows_out.append(row_vals)

    return rows_out


def detect_header_row(rows: Sequence[Sequence[str]]) -> Tuple[int, List[str]]:
    """Return (1-based header row index, header values)."""
    header_keywords = {
        "no",
        "nomor",
        "nama",
        "nama murid",
        "nama siswa",
        "nama peserta didik",
        "nis",
        "nisn",
        "npsn",
        "nik",
        "ttl",
        "tempat lahir",
        "tanggal lahir",
        "tgl lahir",
        "jk",
        "jenis kelamin",
        "alamat",
        "agama",
        "kelas",
        "kelompok",
        "group",
        "ayah",
        "ibu",
        "pekerjaan",
        "jabatan",
        "pendidikan",
        "no hp",
        "hp",
        "telp",
        "sarana",
        "barang",
        "jumlah",
        "qty",
        "kuantitas",
        "kondisi",
        "sumber dana",
        "tahun",
        "bulan",
        "periode",
        "pembayaran",
        "iuran",
        "uang pembangunan",
        "seragam",
        "buku paket",
        "sampul rapor",
    }

    def cell_header_score(cell: str) -> int:
        raw = (cell or "").strip()
        if raw == "":
            return 0

        n = raw.lower().strip()
        n_simple = re.sub(r"\s+", " ", re.sub(r"[^a-z0-9 ]+", " ", n)).strip()

        score = 0

        # keyword boost
        if n_simple in header_keywords:
            score += 10
        else:
            for kw in header_keywords:
                if kw in n_simple and len(kw) >= 3:
                    score += 4
                    break

        # contains letters -> likely a label
        if re.search(r"[a-zA-Z]", raw):
            score += 2

        # penalize values that look like data
        if re.search(r"\d{4}-\d{2}-\d{2}", raw):
            score -= 2
        if "," in raw:
            score -= 1
        if len(raw) > 35:
            score -= 2

        return score

    best_i = 0
    best_row_score = -10**9
    best_filled = 0
    best_vals: List[str] = []

    for i, row in enumerate(rows, start=1):
        filled = [c.strip() for c in row if (c or "").strip() != ""]
        if len(filled) < 2:
            continue

        row_score = sum(cell_header_score(c) for c in row)
        # small bonus for more filled cells
        row_score += min(len(filled), 50)

        if (row_score > best_row_score) or (row_score == best_row_score and len(filled) > best_filled):
            best_i = i
            best_row_score = row_score
            best_filled = len(filled)
            best_vals = [c.strip() for c in row]

    if best_i == 0:
        first = [c.strip() for c in (rows[0] if rows else [])]
        return 1, first

    while best_vals and best_vals[-1] == "":
        best_vals.pop()

    return best_i, best_vals


# ------------------------- Migrations parsing -------------------------

_TABLE_CREATE_RE = re.compile(r"Schema::create\(\s*'([^']+)'\s*,")
_TABLE_DROP_RE = re.compile(r"Schema::dropIfExists\(\s*'([^']+)'\s*\)")
_TABLE_MOD_RE = re.compile(r"Schema::table\(\s*'([^']+)'\s*,")

# Capture column names from common blueprint calls.
# Examples:
#   $table->string('name');
#   $table->id('id_student');
#   $table->foreignId('id_user')->constrained(...)
_COL_RE = re.compile(
    r"\$table->(?:id|foreignId|string|text|longText|integer|unsignedInteger|unsignedBigInteger|unsignedTinyInteger|bigInteger|boolean|date|dateTime|timestamp|enum|json|decimal|float)\(\s*'([^']+)'"
)


def _extract_block(lines: List[str], start_idx: int) -> Tuple[List[str], int]:
    """Extract a PHP closure block starting at start_idx (best-effort)."""
    buf: List[str] = []
    depth = 0
    started = False

    i = start_idx
    while i < len(lines):
        line = lines[i]
        if not started:
            # start counting from first '{'
            if "{" in line:
                started = True
                depth += line.count("{")
                depth -= line.count("}")
                buf.append(line)
            i += 1
            continue

        depth += line.count("{")
        depth -= line.count("}")
        buf.append(line)

        if started and depth <= 0:
            return buf, i
        i += 1

    return buf, len(lines) - 1


def extract_schema_from_migrations(migrations_dir: str) -> Dict[str, List[str]]:
    """Return table -> sorted column names (best effort)."""
    schema: Dict[str, Dict[str, None]] = {}

    if not os.path.isdir(migrations_dir):
        return {}

    migration_files = [
        os.path.join(migrations_dir, f)
        for f in os.listdir(migrations_dir)
        if f.endswith(".php")
    ]
    migration_files.sort(key=lambda p: os.path.basename(p))

    for path in migration_files:
        try:
            with open(path, "r", encoding="utf-8", errors="replace") as f:
                lines = f.read().splitlines()
        except OSError:
            continue

        # Handle dropIfExists anywhere in file
        for line in lines:
            m_drop = _TABLE_DROP_RE.search(line)
            if m_drop:
                schema.pop(m_drop.group(1), None)

        # Find create/table blocks
        for idx, line in enumerate(lines):
            m_create = _TABLE_CREATE_RE.search(line)
            if m_create:
                table = m_create.group(1)
                block, _ = _extract_block(lines, idx)
                cols = set(_COL_RE.findall("\n".join(block)))

                # Common Laravel helpers not caught by regex
                if "rememberToken" in "\n".join(block):
                    cols.add("remember_token")
                if "timestamps" in "\n".join(block):
                    cols.add("created_at")
                    cols.add("updated_at")

                schema[table] = {c: None for c in cols}
                continue

            m_mod = _TABLE_MOD_RE.search(line)
            if m_mod:
                table = m_mod.group(1)
                block, _ = _extract_block(lines, idx)
                cols = set(_COL_RE.findall("\n".join(block)))

                if not cols:
                    continue
                if table not in schema:
                    schema[table] = {}
                for c in cols:
                    schema[table][c] = None

    return {t: sorted(cols.keys()) for t, cols in schema.items()}


# ------------------------- Matching + scoring -------------------------

def classify_excel_file(filename: str) -> str:
    n = _norm(os.path.basename(filename))
    if any(k in n for k in ["siswa", "murid", "student"]):
        return "students"
    if any(k in n for k in ["guru", "teacher", "ustadz"]):
        return "teachers"
    if any(k in n for k in ["absensi", "attendance", "daftarhadir", "hadir"]):
        return "attendance"
    if any(k in n for k in ["pembayaran", "payment", "iuran", "spp", "laporanbulanan", "bulanan"]):
        return "payments"
    if any(k in n for k in ["sarana", "facility", "inventaris"]):
        return "facilities"
    return "unknown"


def build_candidate_db_fields(schema: Dict[str, List[str]], category: str) -> Dict[str, str]:
    """Return normalized_field -> full_field (table.column)."""
    focus_tables: List[str]
    if category == "students":
        focus_tables = ["students", "parents", "classes", "class_student", "registrations", "users"]
    elif category == "teachers":
        focus_tables = ["teacher_details", "teacher_attendance", "teacher_honors", "users"]
    elif category == "attendance":
        focus_tables = ["student_attendance", "teacher_attendance", "students", "teacher_details", "classes", "class_student", "class_teacher"]
    elif category == "payments":
        focus_tables = ["payments", "student_payments", "student_payment_installments", "payment_methods", "payment_proofs", "students"]
    elif category == "facilities":
        focus_tables = ["facilities"]
    else:
        focus_tables = sorted(schema.keys())

    out: Dict[str, str] = {}
    for t in focus_tables:
        cols = schema.get(t) or []
        for c in cols:
            out[_norm(c)] = f"{t}.{c}"

    # A few common Indonesian header aliases (best-effort)
    alias = {
        "namasiswa": "students.name",
        "nama": "students.name" if category in {"students", "attendance"} else "teacher_details.name" if category == "teachers" else None,
        "tempatlahir": "students.birth_place",
        "tgllahir": "students.birth_date",
        "tanggallahir": "students.birth_date",
        "jeniskelamin": "students.gender",
        "jk": "students.gender",
        "kelas": "classes.class_name",
        "tahunajaran": "classes.school_year",
        "namaayah": "parents.father_name",
        "namaibu": "parents.mother_name",
        "nohpayah": "parents.father_phone_num",
        "nohpibu": "parents.mother_phone_num",
        "alamatayah": "parents.father_address",
        "alamatibu": "parents.mother_address",
        "status": "students.status" if category == "students" else None,
        "bulan": "student_payments.payment_period" if category == "payments" else None,
        "periode": "student_payments.payment_period" if category == "payments" else None,
        "nominal": "student_payments.final_amount" if category == "payments" else None,
        "jumlah": "student_payments.final_amount" if category == "payments" else None,
        "metodepembayaran": "student_payments.payment_method" if category == "payments" else None,
    }
    for k, v in alias.items():
        if v:
            out.setdefault(k, v)

    return out


def score_headers(headers: Sequence[str], candidates: Dict[str, str]) -> Tuple[float, List[Dict[str, str]]]:
    matches: List[Dict[str, str]] = []
    matched = 0
    total = 0

    for h in headers:
        if (h or "").strip() == "":
            continue
        total += 1
        hn = _norm(h)
        if hn in candidates:
            matched += 1
            matches.append({"excel": h, "db": candidates[hn], "match": "exact"})
        else:
            # soft match: substring against candidate keys
            best = None
            for ck, full in candidates.items():
                if hn and (hn in ck or ck in hn):
                    best = full
                    break
            if best:
                matched += 1
                matches.append({"excel": h, "db": best, "match": "fuzzy"})
            else:
                matches.append({"excel": h, "db": "", "match": "missing"})

    score = (matched / total * 100.0) if total else 0.0
    return round(score, 2), matches


def audit_xlsx_file(path: str, max_rows: int) -> Dict:
    with zipfile.ZipFile(path, "r") as z:
        shared = _parse_shared_strings(_safe_read_zip_text(z, "xl/sharedStrings.xml"))
        sheets = _parse_workbook_sheets(z)

        file_info = {
            "file": os.path.basename(path),
            "category": classify_excel_file(path),
            "sheets": [],
        }

        for s in sheets:
            rows = _extract_sheet_rows(z, s.xml_path, shared, max_rows=max_rows)
            header_row, headers = detect_header_row(rows)

            file_info["sheets"].append(
                {
                    "name": s.name,
                    "headerRow": header_row,
                    "headers": headers,
                    "headersNormalized": [_norm(h) for h in headers],
                }
            )

        return file_info


def main(argv: Optional[Sequence[str]] = None) -> int:
    p = argparse.ArgumentParser()
    p.add_argument("--excel-dir", required=True, help="Directory containing .xlsx files")
    p.add_argument("--out", required=True, help="Output JSON report path")
    p.add_argument(
        "--migrations-dir",
        default=None,
        help="If provided, also extracts schema from this migrations directory and performs header->DB matching",
    )
    p.add_argument("--max-rows", type=int, default=60, help="Rows scanned per sheet for header detection")

    args = p.parse_args(argv)

    excel_dir = os.path.abspath(args.excel_dir)
    out_path = os.path.abspath(args.out)

    if not os.path.isdir(excel_dir):
        print(f"Excel dir not found: {excel_dir}", file=sys.stderr)
        return 2

    schema: Dict[str, List[str]] = {}
    if args.migrations_dir:
        schema = extract_schema_from_migrations(os.path.abspath(args.migrations_dir))

    xlsx_files = [
        os.path.join(excel_dir, f)
        for f in os.listdir(excel_dir)
        if f.lower().endswith(".xlsx") and not f.startswith("~$")
    ]
    xlsx_files.sort(key=lambda p: os.path.basename(p).lower())

    report = {
        "generatedAt": _dt.datetime.now().isoformat(timespec="seconds"),
        "excelDir": os.path.relpath(excel_dir, os.getcwd()),
        "fileCount": len(xlsx_files),
        "files": [],
        "dbSchema": {"tables": len(schema), "schema": schema} if schema else None,
    }

    for fpath in xlsx_files:
        info = audit_xlsx_file(fpath, max_rows=max(1, args.max_rows))

        if schema:
            category = info.get("category", "unknown")
            candidates = build_candidate_db_fields(schema, category)

            for sh in info.get("sheets", []):
                score, matches = score_headers(sh.get("headers", []), candidates)
                sh["matchScore"] = score
                sh["matches"] = matches

        report["files"].append(info)

    os.makedirs(os.path.dirname(out_path), exist_ok=True)
    with open(out_path, "w", encoding="utf-8") as f:
        json.dump(report, f, ensure_ascii=False, indent=2)

    print(f"Wrote report: {os.path.relpath(out_path, os.getcwd())}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

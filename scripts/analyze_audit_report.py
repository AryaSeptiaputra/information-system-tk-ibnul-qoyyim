#!/usr/bin/env python3
"""Analyze storage/app/excel_audit/report.json and print gaps.

Outputs:
- per file/sheet: matchScore, header count, missing count
- missing header labels (non-empty)
- detects pivot-style headers (mostly numeric days or month names)

No external deps.
"""

from __future__ import annotations

import json
import re
from pathlib import Path
from typing import Iterable, List

MONTHS = {
    "januari",
    "jan",
    "februari",
    "feb",
    "maret",
    "mar",
    "april",
    "apr",
    "mei",
    "juni",
    "jun",
    "juli",
    "agu",
    "agustus",
    "agt",
    "agstus",
    "september",
    "sep",
    "septe",
    "oktober",
    "okt",
    "okto",
    "november",
    "nov",
    "desember",
    "des",
}


def _clean(s: str) -> str:
    return re.sub(r"\s+", " ", (s or "").strip())


def _is_day_number(s: str) -> bool:
    s = (s or "").strip()
    if not re.fullmatch(r"\d{1,2}", s):
        return False
    v = int(s)
    return 1 <= v <= 31


def _is_month_label(s: str) -> bool:
    s = re.sub(r"[^a-zA-Z]", "", (s or "").strip()).lower()
    if s in MONTHS:
        return True
    # allow prefixes like 'septe.' -> 'septe'
    for m in MONTHS:
        if len(s) >= 3 and (s.startswith(m) or m.startswith(s)):
            return True
    return False


def _pivot_hint(headers: List[str]) -> str:
    non_empty = [h for h in headers if _clean(h)]
    if not non_empty:
        return ""

    day = sum(1 for h in non_empty if _is_day_number(h))
    mon = sum(1 for h in non_empty if _is_month_label(h))

    if day / len(non_empty) >= 0.6 and day >= 10:
        return "pivot:days"
    if mon / len(non_empty) >= 0.4 and mon >= 6:
        return "pivot:months"
    return ""


def main() -> int:
    report_path = Path("storage/app/excel_audit/report.json")
    if not report_path.exists():
        print(f"Report not found: {report_path}")
        return 2

    rep = json.loads(report_path.read_text(encoding="utf-8"))

    print("generatedAt:", rep.get("generatedAt"))

    for f in rep.get("files", []):
        sheets = f.get("sheets", []) or []
        scored_sheets = []
        for sh in sheets:
            headers = sh.get("headers") or []
            headers_ne = [_clean(h) for h in headers if _clean(h)]
            score = sh.get("matchScore")
            if headers_ne and isinstance(score, (int, float)):
                scored_sheets.append(float(score))

        avg = round(sum(scored_sheets) / len(scored_sheets), 2) if scored_sheets else None

        print("\n===", f.get("file"), "===")
        if avg is not None:
            print("fileAvgMatchScore:", avg)

        for sh in sheets:
            headers = sh.get("headers") or []
            headers_ne = [_clean(h) for h in headers if _clean(h)]
            matches = sh.get("matches") or []
            missing = [m.get("excel") for m in matches if m.get("match") == "missing" and _clean(m.get("excel", ""))]

            hint = _pivot_hint(headers_ne)
            score = sh.get("matchScore")
            print("- sheet:", sh.get("name"))
            print("  headerRow:", sh.get("headerRow"), "| headers:", len(headers_ne), "| matchScore:", score, ("| " + hint if hint else ""))
            if missing:
                print("  missing (sample):", ", ".join(missing[:20]), ("..." if len(missing) > 20 else ""))
            else:
                print("  missing: (none)")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

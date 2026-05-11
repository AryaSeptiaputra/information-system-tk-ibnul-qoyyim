#!/usr/bin/env python3
"""Print a compact summary from storage/app/excel_audit/report.json.

This avoids PowerShell quoting issues when using python -c.
"""

from __future__ import annotations

import json
from pathlib import Path


def main() -> int:
    report_path = Path("storage/app/excel_audit/report.json")
    if not report_path.exists():
        print(f"Report not found: {report_path}")
        return 2

    rep = json.loads(report_path.read_text(encoding="utf-8"))

    print("generatedAt:", rep.get("generatedAt"))

    for f in rep.get("files", []):
        sheets = f.get("sheets") or []
        print("\nFILE:", f.get("file"), "| category=", f.get("category"), "| sheets=", len(sheets))
        for sh in sheets:
            headers = sh.get("headers") or []
            non_empty = [h for h in headers if str(h).strip()]
            sample = non_empty[:12]
            print("  - sheet:", sh.get("name"))
            print("    headerRow:", sh.get("headerRow"), "| matchScore:", sh.get("matchScore"))
            print("    headerSample:", sample)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

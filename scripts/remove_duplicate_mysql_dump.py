#!/usr/bin/env python3
"""Remove duplicated MySQL dump content accidentally appended to the same file.

This script looks for multiple occurrences of the standard dump header starting with
`CREATE DATABASE` / `USE` and keeps only the first full dump.
"""

from __future__ import annotations

import argparse
from pathlib import Path


MARKER = "CREATE DATABASE  IF NOT EXISTS"


def dedupe_dump(content: str) -> str:
    first = content.find(MARKER)
    if first == -1:
        return content

    second = content.find(MARKER, first + len(MARKER))
    if second == -1:
        return content

    # Keep everything up to the second dump start.
    cleaned = content[:second]

    # If text before the second marker was merged into the final line,
    # remove trailing marker fragments.
    if cleaned.rstrip().endswith(";" + MARKER):
        cleaned = cleaned.rstrip()[: -len(MARKER)]

    return cleaned.rstrip() + "\n"


def main() -> None:
    parser = argparse.ArgumentParser(description="Remove duplicated MySQL dump appended in same file")
    parser.add_argument("input", type=Path, help="Path to SQL dump")
    parser.add_argument("-o", "--output", type=Path, help="Output file (defaults to in-place)")
    args = parser.parse_args()

    original = args.input.read_text(encoding="utf-8")
    cleaned = dedupe_dump(original)

    output_path = args.output or args.input
    output_path.write_text(cleaned, encoding="utf-8")

    if cleaned == original:
        print("No duplicate dump marker found; file unchanged.")
    else:
        print(f"Removed duplicated dump section and wrote: {output_path}")


if __name__ == "__main__":
    main()

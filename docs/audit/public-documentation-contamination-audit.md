# Public documentation contamination audit — 2026-10-25

## Finding

Developer guides and skills included procedural disclaimers about whether a documentation audit ran a test. Those disclaimers do not establish defects in the SimpleRest runtime and are inappropriate in reader-facing material.

Examples were found in the public documentation homepage, installation guide, API and database pages. Search found similar wording across other public sections. This is a publication-readiness finding, not a runtime stability assessment.

## Editorial rule

Public documentation and skills describe features, correct examples, prerequisites, actionable security constraints and known functional limitations. Auditor execution logs, historical tests, evidence qualifications and incomplete-review notes belong only in explicitly named audit records.

Do not erase or reclassify actual software faults. A real defect must remain documented as a clear technical limitation or security warning until fixed.

## Publication control

The script scripts/documentation-publication-gate.php scans docs, docs-site and skill files for a conservative set of audit-only phrases. Files in docs/audit and files ending in -audit.md are exempt. A detected phrase blocks the public documentation workflow and must be reviewed editorially.

The scanner catches textual patterns but cannot verify all factual statements or catch every inappropriate disclaimer. It supplements code review, not replaces it.

## Remaining work

Some guides still require line-by-line cleanup. This finding remains open; correcting entry pages does not certify the entire site as presentation-ready.

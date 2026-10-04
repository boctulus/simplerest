# Repository guidance for agents

## Canonical documentation

Before changing documentation, read [`docs/documentation-governance.md`](docs/documentation-governance.md) and [`docs/canonical-manifest.json`](docs/canonical-manifest.json).

- `canonical_files` lists the protected canonical source. [`docs/audit/documentation-inventory.json`](docs/audit/documentation-inventory.json) records the current classification of every Markdown document under `docs/`; refresh it when paths or links change. Do not move a document as part of unrelated work.
- Make localized, evidence-backed changes to canonical documents. Preserve design intent, decisions, history, and uncertainty. Do not regenerate a canonical document or large section for style consistency.
- If code and canonical documentation disagree, record and classify the discrepancy before changing either source. Current code does not automatically overrule documented intent.
- Deletion, rename, or substantial content removal requires explicit maintainer approval. The guard is `php scripts/documentation-governance-guard.php`; run it after editing documentation. Its configured approval override is for changes already approved by the maintainer.
- The public Spanish documentation and translations are derived from the canonical source and do not become independent authorities.

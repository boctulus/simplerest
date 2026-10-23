# CLAUDE.md — SimpleRest Framework Rules

## Canonical framework changes and Composer PACK (MANDATORY)

- Shared SimpleRest changes belong in `D:\laragon\www\simplerest` on `master`; never edit GoProp's vendor copy or a release worktree as authoritative source.
- The **microcore** must not contain compatibility shims, aliases, or references to optional packages. Files, database, ACL, legacy CLI, HTML and Bootstrap belong outside the minimal Composer runtime, but their development consumers must be migrated and tested rather than silently broken.
- `php com pack build --profile=core` is the **sole curation entrypoint** for a new core package. It is independent of legacy app.php and generates only the committed microcore.
- Composer snapshots **must** use `scripts/composer/build-core-source-snapshot.ps1 -CuratedDirectory <pack-dir>`. Do not run Git archive of the development monorepo as a separate Composer release source.
- Before touching `D:\laragon\www\simplerest-pack`, inspect Git status, tracked/untracked files, and expected ownership. PACK must fail instead of deleting, resetting, hiding, or overwriting unrelated files. Existing distributions may need an explicitly approved one-time migration.
- Commit source modifications, run the isolated microcore, CLI, pack/snapshot and consumer gates, and record source/distribution SHAs. Never tag, push a release, repoint Packagist, or change a public repository without explicit release authorization.
- `app/LegacyRuntime/autoload.php` is an explicit **application-only** migration bridge. It must not be loaded by a microcore Composer consumer and should eventually be replaced by proper optional packages.
- Source of truth for release details: `docs/architecture/composer-release-process.md`.

---

## 1. Stack (FIXED)

### Backend
- PHP

### Databases
- MySQL (primary)
- Other engines supported via adapters

### Testing
- PHPUnit → unit tests
- Playwright **(preferred)** → E2E / UI (via Node.js)
- Puppeteer → optional
- Selenium → available (Python)

---

## 2. Mandatory Pre-Flight

Before **any** task:

1. Check `docs/`
2. Read root `README.md`
3. Read module / package / component `.md` docs

Key locations:
- `docs/issues/`

## Rule Priority (MANDATORY)

SKILLs override this document.
If a SKILL exists, ignore conflicting content here.

---

## 3. CLI System

- Create CLI commands for repetitive / general tasks
- Always inspect available commands first

Docs:
```
docs/cli/README.md
```

---

## 4. Scripts & Automation Rules

### File placement
- General scripts → `scripts/`
- Temporary scripts → `scripts/tmp/` (DELETE after use)
- Debug / tests → `tests/` or `tests/unit-tests/`
- WebDriver automation → `automation/`

### Forbidden
- ❌ temp files in project root
- ❌ screenshots in root
- ❌ `test_*.js` or `test-*.js` in root
- ❌ scraping/debug via scripts

Reports:
- Unit tests → `reports/unit-tests/`
- Automation → `reports/automation/`

---

## 5. Testing Order (MANDATORY)

1. Backend / API (curl / direct)
2. Then UI
3. Never the reverse

Base URL:
```
APP_URL=<value from .env>
```

---

## 6. Architecture & Development Rules

- Use **modular architecture**
- Follow existing patterns
- Incremental changes only
- Major refactors → justify + ask
- DRY
- SOLID / Clean Code
- KISS
- No fallbacks without approval
- Business logic ≠ routing
- Middleware for complexity
- APIs must be documented

Read:
```
docs/core/README.md
```

---

## 7. Routing

- Review routing documentation before implementing
- Packages may define their own `routes.php` via ServiceProvider
- Avoid duplicating routes
- If routes fail:
  - verify package registration
  - run `composer dump-autoload`

---

## 8. API Consumption

- Use `ApiClient`
- ❌ No direct CURL usage

Docs:
```
docs/api/README.md
```

---

## 9. Mandatory Task Steps

For **every** task:

- Save user prompt to:
  ```
  prompts/{yyyy-mm-dd hh-mm} {git-hash}.txt
  ```
- Read relevant docs
- Plan tests **before** implementation
- Test before considering task complete

---

## 10. Documentation Rules

Documentation preserves technical facts, design intent, decisions, and project history. Canonical documentation must not be regenerated freely.

Before changing documentation, read `docs/documentation-governance.md`, `docs/canonical-manifest.json`, and `docs/audit/documentation-inventory.json`. The manifest identifies protected canonical sources; the inventory records the current classification of every Markdown document under `docs/`.

- Make localized, evidence-backed changes to canonical documents and preserve intent, context, and uncertainty.
- Do not rewrite a canonical document or large section for style consistency.
- Classify code/documentation contradictions before editing; current code does not automatically overrule documented intent.
- Run `php scripts/documentation-governance-guard.php` after documentation changes. Deletion, rename, or substantial removal requires explicit maintainer approval.
- Public Spanish pages and translations are derived artifacts, not independent authorities.

Location rules:
- Commands → `docs/cli/README.md`
- Components → `docs/components/`
- Modules → `docs/modules/`
- Packages → `docs/packages/`
- Issues → `docs/issues/`
- Major changes → update the relevant canonical page; legacy changelogs are historical references only.

Always ask at task end:
> Can documentation be created or updated?

---

## 11. Security (STRICT)

❌ Never delete:
- production tables
- master tables
- source-of-truth data

✔️ Test data may be deleted.

Any destructive operation requires **explicit approval**.

---

## 12. Development Environment

- Windows 11 + WSL2
- GNU tools available
- Docker available
- Docker workdirs:
  - `D:\Docker`
  - `D:\Pabloo\Docker`

---

## 13. Known Framework Differences

- `String::contains()` parameter order differs from PHP native
- Query Builder ≠ Laravel:
  - `DB::table()` requires schema file
  - `table()` helper does not

---

## Author

```
Pablo Bozzolo (boctulus)
Software Architect
```

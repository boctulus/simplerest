# CLAUDE.md — SimpleRest Framework Rules

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
- `docs/login-credentials.md`
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
docs/CommandLine.md
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

Credentials:
```
docs/login-credentials.md
```

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
docs/core-directives.md
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
docs/ApiClient.md
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

Documentation is **LLM feedback**, not prose.

Location rules:
- Commands → `docs/commands/`
- Components → `docs/components/`
- Modules → `docs/modules/`
- Packages → `docs/packages/`
- Issues → `docs/issues/`
- Major changes → `docs/CHANGELOG-*.md`

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

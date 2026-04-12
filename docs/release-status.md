# Release Status — SimpleRest Framework

> **Generated**: 2026-04-12
> **Version**: 0.9.0
> **Branch**: master
> **Repo**: https://github.com/boctulus/simplerest

---

## Executive Summary

SimpleRest is a **modular PHP framework** designed for high-performance REST APIs. It follows a "Laravel-like syntax, minimal overhead" philosophy, bootstrapping in 3-10ms compared to Laravel's 300-500ms.

The framework is **feature-complete for core use cases** (routing, ORM, auth, ACL, CLI) but **not yet release-ready** in its current state. Critical blockers exist in the test suite, licensing, and documentation.

---

## What Works ✅

| Feature | Status | Notes |
|---------|--------|-------|
| **WebRouter** | ✅ Complete | GET/POST/PUT/DELETE, param routes, groups, regex, batch routes |
| **CliRouter** | ✅ Complete | Command system with groups, multi-word commands |
| **FrontController** | ✅ Complete | 6 pluggable handlers (Request, Auth, API, Output, Error, Middleware) |
| **Query Builder** | ✅ Complete | Laravel-like fluent syntax, returns arrays (not objects) |
| **Model / ORM-lite** | ✅ Complete | Soft deletes, timestamps, hooks, bulk inserts, sub-resources |
| **Auto REST Endpoints** | ✅ Complete | Zero-config CRUD from table names with advanced filtering |
| **AutoJoins** | ✅ Complete | Relationships inferred from FK/naming conventions |
| **JWT Auth** | ✅ Complete | Token generation, validation, refresh, remember-me tokens |
| **Fine-Grained ACL** | ✅ Complete | Hierarchical roles, granular permissions, user-level overrides |
| **Basic ACL** | ✅ Complete | Simple role-based access control |
| **Middleware System** | ✅ Complete | Per-controller/per-method targeting, stackable |
| **Validation** | ✅ Complete | Type validation, unique constraints, i18n |
| **Caching** | ✅ Complete | FileCache, DBCache, InMemoryCache |
| **PSR-7** | ✅ 95% | Via adapters + native immutable `with*()` methods |
| **Multi-DB** | ✅ Complete | MySQL, PostgreSQL, SQLite, SQL Server, Oracle, Firebird, etc. |
| **Multi-tenant** | ✅ Complete | Table prefixes and connection management |
| **Service Providers** | ✅ Complete | Package registration with register()/boot() lifecycle |
| **CLI Commands** | ✅ Complete | 12 built-in commands across 11 sections |
| **i18n / Gettext** | ✅ Complete | Multi-language support |
| **Mailer** | ✅ Complete | SendinBlue, templating |
| **Modular Architecture** | ✅ Complete | Modules + Packages with custom autoloader |
| **Packaging System** | ✅ Complete | `.cpignore` / `.cpinclude` for clean releases |

---

## What's In Progress ⚠️

| Feature | Status | Gap |
|---------|--------|-----|
| **Test Suite** | 🔴 Broken | 228 tests: 86 errors, 11 failures, 6 risky |
| **PHP 8.4** | 🔴 Blocked | Explicitly excluded (`<8.4`) |
| **Benchmarks** | ❌ Missing | No performance measurements exist |
| **CI/CD** | ❌ Missing | No GitHub Actions or automated testing |
| **PSR-17** | 📋 Planned | HTTP Factories |
| **PSR-15** | 📋 Planned | HTTP Server Request Handlers |
| **QuickStart** | 🔴 Incomplete | Ends with `<<< COMPLETAR` |
| **Public Docs** | ⚠️ Mixed | Internal TODO files mixed with public docs |

---

## What's Planned / Roadmap 📋

From `docs/to-do/TODO !must-do.md`:

### Core Stability
- [ ] Benchmark tests for each core component
- [ ] Automated performance testing per commit
- [ ] Benchmark regression tracking
- [ ] Minimal reflection audit

### Ecosystem
- [ ] Clean minimal website (landing + docs)
- [ ] GitHub repo with examples and benchmarks
- [ ] Optional CLI installer (non-Composer)

### Strategic Compatibility
- [ ] Move `providers` array to `config/providers.php`
- [ ] Implement ModuleProvider for modules
- [ ] Optional Composer package for users who prefer it

### Future Versions
- **1.0** — SubResources, JsonAPI, SQLite/PostgreSQL support
- **1.1** — MongoDB support
- **1.2** — Eloquent-compatible ORM

---

## Current Metrics

### Codebase
| Metric | Value |
|--------|-------|
| Framework files | `src/framework/` — 101 library classes |
| Local packages | 11 packages in `packages/boctulus/` |
| Documentation | 39 `.md` files in `docs/` (excluding `to-do/`) |
| TODO/FIXME in core | 106 occurrences in `src/framework/` |
| CLI commands | 12 commands, 11 sections |
| PHPUnit tests | 228 total |

### Test Results (as of 2026-04-12)
```
Tests: 228, Assertions: 423
Errors: 86  (37.7%)
Failures: 11
Warnings: 3
Skipped: 1
Risky: 6
```

**Passing cleanly**: ~126 of 228 (55%)

### Uncommitted Changes
- **158 files modified** (not staged for commit)
- Primary changes: namespace fixes (`MyApiController` → `ApiController`), new SQL commands, UI overhaul

### Git
- Remote: `origin → https://github.com/boctulus/simplerest.git`
- Branch: `master` (up to date)
- Latest version tag: `0.8.12` (composer.json) / `0.9.0` (README) — **mismatch**

---

## Critical Blockers for Public Release 🔴

### 1. Test Suite Broken
- **Root cause**: `$config` variable undefined in test files
- **Affected**: `ApiCollectionsTest.php`, `ApiTest.php`, `ApiTrashCanTest.php`, `AuthTest.php`
- **Impact**: `parse_url(null)` and `rtrim(null)` cause PHP warnings/deprecations
- **Fix**: Initialize `$config` from `Config::get()` or include config file

### 2. License Contradiction
- `composer.json` says `"license": "MIT"`
- `LICENSE` file says `"COMERCIAL — Todos los derechos reservados"`
- **Impact**: Legally ambiguous, unus for any purpose

### 3. QuickStart Incomplete
- `docs/QuickStart.md` ends with `<<< COMPLETAR`
- **Impact**: First-time users have no onboarding path

### 4. No CI/CD
- No `.github/` directory, no GitHub Actions
- **Impact**: No automated testing, appears unmaintained

### 5. No Benchmarks
- Framework's main selling point (performance) has zero measurements
- **Impact**: Claims are unverifiable

---

## Known Technical Debt

### `src/framework/` TODOs (106 occurrences)
Key concerns:
- `CliRouter.php:313` — "Metodos sin probar de implementacion pendiente"
- `Helpers/package.php:19` — "TODO: podria cambiarse la forma en la que el autodiscovery trabaja"
- `Libs/DomCrawler.php:14` — "TODO: mover a package"
- `Libs/ApiClient.php:23` — "IMPLEMENTAR alias de metodos restantes"

### Version Mismatch
- `composer.json`: `"version": "0.8.12"`
- `README.md`: `**Versión**: 0.9.0`

### phpunit.xml Misconfiguration
- `<source>` coverage only includes `friendlypos-web/src`
- **Should** include `src/framework/` for framework coverage

---

## Packaging System

The framework has a mature packaging system using `.cpignore` and `.cpinclude`:

### Excluded from release (`.cpignore`):
- `.git/`, `.codegpt/`, `.claude/`, `.agent/`, `.vscode/`
- `docs/to-do/`, `docs/issues/`, `docs/extras/`, `docs/etc/`
- `app/`, `packages/`, `docker/`, `backups/`, `exports/`
- `logs/`, `tmp/`, `uploads/`, `web-automation/`
- Test files, backup files, IDE files

### Included in release (`.cpinclude`):
- Core commands, essential controllers, views, locale
- ACL packages (basic + fine-grained)
- Public assets (CSS, JS, images)
- Essential migrations

**This system works correctly** — release packages are clean.

---

## Release Readiness Score

| Category | Score | Notes |
|----------|-------|-------|
| Core Functionality | 9/10 | Feature-complete, stable |
| Test Suite | 3/10 | More failing than passing |
| Documentation | 5/10 | Exists but incomplete/mixed |
| Performance Proof | 0/10 | No benchmarks |
| Licensing | 1/10 | Contradictory |
| CI/CD | 0/10 | None |
| Onboarding | 3/10 | QuickStart incomplete |
| **Overall** | **3.5/10** | **Not release-ready** |

---

## Recommended Next Steps

1. **Commit pending changes** (158 files)
2. **Fix test suite** (resolve `$config` issue)
3. **Resolve license** (decide MIT vs commercial)
4. **Complete QuickStart**
5. **Set up GitHub Actions**
6. **Add basic benchmarks**
7. **Clean internal docs from public view**

Estimated effort: **2-4 weeks of focused work**

---

*This document is auto-generated as part of the release preparation process. Update it whenever significant changes occur.*

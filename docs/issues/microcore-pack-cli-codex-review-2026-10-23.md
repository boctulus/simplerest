# P0 — Codex review: microcore, restored CLI and PACK-first Composer distribution

Date: 2026-10-23
Status: OPEN — review required; publication NOT authorized
Scope: SimpleRest `master` only. Do not modify GoProp or production.

## Observed state

- `a128255` introduced a minimal 14-class HTTP kernel in `src/framework/`.
- Concurrent commit `f99f0fd` reintroduced historical implementations in `src/framework/`. Current tree at `4b2e491` contains 227 files, and CI correctly fails `MicrocoreIsolationTest` because it detects PDO/PSR-7/ORM code. Preserve the other session's work; do not indiscriminately revert or delete it.
- `Files.php`, `CommandRegistry`, legacy Request/Response, and other application classes were archived by the initial extraction. `app/LegacyRuntime/autoload.php` and `app/LegacyCore/` provide a temporary application-only bridge. The user's Windows `php com` had failed at `app.php:93` with `Class ...Core\Libs\Files not found`. Full CLI compatibility is NOT verified.
- `config/autoload.php` was adjusted to load archived helpers instead of the now-obsolete CORE_HELPERS_PATH. Audit this against both restored source and actual application behavior.
- `php com pack build --profile=core` bypasses the broken application bootstrap and curates only files enumerated by the immutable `scripts/pack/core-files.txt` manifest (14 PHP classes + composer.json, README.md, LICENSE). It does not read the entire development tree or overwrite dirty destinations.
- `scripts/composer/build-core-source-snapshot.ps1` now consumes that curated PACK output and verifies each file against an immutable source commit. No tag, release or Packagist publication was performed.
- The PACK curation test has its own CI job, separate from the red architectural regression. Do not weaken microcore tests just to make CI green.
- The public GitHub source monorepo is still visible, with historical docs/archive files. A clean local PACK is not sufficient to sanitize the public Git repository. Packagist's eventual publication must resolve to a clean distribution Git remote with old immutable tags preserved.

## Mandatory CODEX tasks

1. Inspect current Git refs, worktrees, owners of changed files, and all concurrent commits before editing. Reconcile the 227-file framework restoration against the microcore contract. Do not reset, force-push, discard unrelated changes or weaken CI assertions.
2. Restore the developer CLI `php com` (no arguments) and `php com help` in Windows/Laragon without injecting the legacy application into the Composer microcore. Exercise command discovery and a representative safe command. Avoid creating `.env`, touching production DBs or running unapproved migrations.
3. Classify essential utilities such as Files, Strings, Arrays, Date, URL, CLI, ORM and ACL by optional package ownership. A class moved to archive is NOT automatically available to consumers. Plan a real Composer-compatible migration for each.
4. Test on disposable paths: `php com pack build --profile=core --dry-run`, the real curated PACK, dirty-destination refusal, and the PowerShell snapshot with matching SourceCommit. Confirm exactly 17 files and absence of docs/, archive/, app/, Bootstrap and historical helpers.
5. Install the snapshot in a completely fresh Composer consumer using --no-dev and verify PHP 8.1–8.4 plus a real GET /health response. Core, legacy application CLI, PACK and consumer checks must each have their own CI evidence.
6. Ensure the public Composer package source really comes from the clean curated distribution repository, not the development monorepo. Propose a safe non-forcing migration of Git remotes and Packagist source while preserving all v2.0.2 and prior tags. Do not perform the remote/Packagist cutover without owner approval.
7. Review code and docs changed in commits `562bb3d`, `178357a`, `4b2e491`, `ec90cce`, and `29d0916`. Correct genuine P0 issues with minimal commits and tests. Note any features of the pre-microcore application not yet migrated.
8. Validate GoProp dependency compatibility separately; do not assume a 14-class HTTP kernel replaces the DB, automatic REST, auth, ACL and impersonation that GoProp requires.

## Release blockers

- The 227-file `src/framework/` tree violates the microcore architecture, independently of the curated allowlist.
- Real Windows CLI compatibility and full legacy-app integration are not certified.
- Composer 3.0 consumer migration, distribution remote separation, tags and Packagist verification are pending.

## Deliverable

Return: exact source/destination class counts; reproducible CLI, pack and consumer test outputs; CI run links; immutable source/package SHA mapping; any changes and commits; owners/risks for unresolved compatibility. Do not publish or deploy.

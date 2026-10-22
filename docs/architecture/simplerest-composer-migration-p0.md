# P0 — GoProp consumes the canonical SimpleRest core through Composer

Status: **proposed for immediate implementation; not implemented**  
Date: 2026-10-22  
Target consumer: `D:\laragon\www\goprop` (`boctulus/goprop`, `main`)  
Framework source: `D:\laragon\www\simplerest` (`boctulus/simplerest`, default branch `master`)  
Related design: [SimpleRest CLI modularity and command extension RFC](simplerest-cli-modularity-rfc.md).

## Decision

**P0 is eliminating the duplicated runtime framework, not completing the package ecosystem.**

Use `boctulus/simplerest` as the sole runtime source of shared `Boctulus\Simplerest\Core\*` classes in GoProp. Keep GoProp's existing `app/Commands/`, `com`, models, migrations, services, configuration, and project-specific packages where they are. Do **not** require a new `goprop-users` or `simplerest-cli-users` package for this cutover.

The near-term target is **one Composer package for the working core**. Optional CLI/domain packages, hooks, and lazy command discovery are subsequent tasks, except for a collision/safety defect that directly blocks P0.

GoProp's application-specific behavior stays in GoProp. General framework fixes are reconciled into the SimpleRest source before the cutover. No production mutation during audit or staging.

## Why this is urgent

GoProp currently maps `Boctulus\Simplerest\Core\` to `src/framework/`. Other SimpleRest-based applications (including ACVAL, iDataSync and the business directory) have independent copies. Manual copying obscures provenance and multiplies divergences. Students or contributors working on GoProp and SimpleRest must be able to share one tested framework change without copying source files.

## Verified repository findings (GitHub snapshot, 2026-10-22)

1. GoProp `composer.json` does **not** require `boctulus/simplerest`; it PSR-4 maps `Boctulus\Simplerest\Core\` to its own `src/framework/` and also maps `Boctulus\Simplerest\` to `src/`.
2. GoProp `config/constants.php` defines `CORE_PATH`, `CORE_HELPERS_PATH`, `CORE_LIBS_PATH`, templates, interfaces and traits using its local `src/framework/` tree. `config/autoload.php` directly includes `src/framework/Helpers`. These **must** be adapted; a Composer manifest edit alone is insufficient.
3. `COMMANDS_PATH` in GoProp points to local `app/Commands/`. `config/commands.php` has empty `paths` and package registrations; current `CommandRegistry` discovers the local command tree by default. Composer does not physically overwrite application commands.
4. The existing registry can replace a logical command registration when another discovery path returns the same `group:command` key. Do not add vendor command paths to P0; collision policy is a separate RFC.
5. GoProp's `com` differs from SimpleRest's `com` (for example group help and environment/config overrides). **Do not replace it** during framework installation.
6. Content comparison confirmed equal Git blobs for `src/framework/Libs/DB.php` and `src/framework/Commands/BaseCommand.php`, but different blobs for `Schema.php`, `CommandRegistry.php` and `FrontController.php`. Example semantic differences: GoProp has an additional PostgreSQL schema branch and uses HTTP 401 for some unauthenticated API access where SimpleRest returns HTTP 403. Compare the **entire** core before deciding direction for each difference.
7. SimpleRest development `composer.json` requires PHP `>=8.1,<8.5` and PHPMailer `^7.1`; GoProp declares PHP `>=7.4,<8.4` and PHPMailer `^6.9`. Those PHPMailer constraints have no compatible intersection. The packager alters the requirements, so verify the **actual package artifact**, not only the development manifest.
8. `php com pack build` uses `D:\laragon\www\simplerest` as source and `D:\laragon\www\simplerest-pack` as output. `scripts/pack_framework.php` transforms the copied tree and Composer manifest. The relationship among development source, curated distribution Git repository and the version actually consumed from Packagist is **not yet established**.
9. The packager deletes destination contents before source validation. **Do not run it on an unverified target path**. Its current release shape includes application skeleton concerns; a library-consumer smoke test is essential.
10. GoProp `app.php` invokes `composer install` when `vendor/autoload.php` is absent. Treat runtime installation as a deployment risk; do not rely on a web request to install the core.

These findings are evidence of migration work, **not** proof that a current Composer release is already safe to install.

## Smallest acceptable P0 sequence

### Gate A — Reconcile one canonical core

- Record repository heads, package version/tag, PHP runtime, extensions, dependencies and lockfiles.
- Compute a **complete file-by-file core inventory** and semantic diff (added, removed, modified, casing, namespace, entrypoints, helpers, templates). Classify each difference: transferable framework fix / application policy / SimpleRest-only fix / unresolved.
- Port transferable improvements to `simplerest/src/framework/` with focused regression tests. Keep GoProp-specific authorization and routing policy configurable, or document an intentional adapter/override. Do not replace GoProp behavior silently.
- Audit direct `require` / `include`, hardcoded paths, class aliases, global helper loading, dynamic discovery, and static `CORE_*` constants. Identify any use that cannot resolve to the package path.
- Confirm package requirements under the **real deployment PHP** (GoProp currently documents PHP 8.3). Resolve PHPMailer and other declared dependency incompatibilities explicitly; never ignore Composer platform requirements.
- Determine **where the consumable package comes from**: the SimpleRest development repository, the `simplerest-pack` generated tree, a separate Git repository/tag, or another release process. Inspect an actual tagged artifact and `composer.json` after packaging. Establish a reproducible release mapping from source commit to distribution commit/tag.

### Gate B — Exercise a clean consumer

- In a disposable GoProp working tree or staging environment, require the canonical core as a Composer dependency, initially through a local `path` repository **if useful**. Use a tagged immutable version and a committed `composer.lock` for reproducible CI/deployment.
- Remove the *active* GoProp `Boctulus\Simplerest\Core\` PSR-4 mapping to `src/framework/`. Assess the broader `Boctulus\Simplerest\` mapping for unwanted shadowing.
- Make `CORE_PATH` and framework helper/template/includes resolve from the installed library, while application paths (including `ROOT_PATH`, `APP_PATH`, `COMMANDS_PATH`, `PACKAGES_PATH`) continue to resolve to GoProp. Decide whether the path is derived from a canonical framework class via reflection or a stable package API; do not assume the vendor directory layout if an API can avoid it.
- Load core helper files **exactly once** and preserve the application-specific helpers after them. Validate case-sensitive Linux paths.
- Do not register CLI commands from `vendor/` at this stage. Keep `app/Commands/` and GoProp's own `com` unchanged, unless a minimal compatibility adaptation is proven necessary.
- Check installed class provenance with `ReflectionClass(...)->getFileName()`: core classes must originate from the installed SimpleRest package, not `goprop/src/framework/`. Add a CI guard prohibiting a second runtime core.
- Exercise a plain HTTP request, authenticated request, unauthorized request (401/403 contract), CLI help, a representative local CLI command, migrations in **disposable databases**, and business-critical operations including payments/financial workflows. Check no scripts or tests write directly to production DBs.
- Run baseline and candidate performance measurements with equivalent PHP, OPcache, dependencies and warm/cold conditions. Compare CLI startup, HTTP bootstrap, median/p95 and peak memory; investigate material regressions without setting an arbitrary performance promise.

### Gate C — Controlled cutover

- Build/stage with a pinned Composer version and lockfile; verify Linux deployment and restored environment config.
- Keep a rollback path to the prior GoProp application commit and prior lockfile; do not delete the copied `src/framework/` history before verification. It can remain physically present but **must not be autoloaded/executed**.
- Release only when critical behavior, provenance, compatibility and rollback checks pass.
- After cutover, freeze manual core copying and require reusable fixes to originate in SimpleRest with consumer regression checks.

## Non-goals for P0

- Splitting every command or command group into Composer packages.
- Replacing GoProp's application-specific `users` commands.
- Moving GoProp `app/Commands/` under `vendor/`.
- Introducing a large service container or full event framework.
- Redesigning all DB migrations or authorization semantics.
- Rewriting the framework for stylistic consistency.
- Claiming better performance than Laravel without reproducible measurements.

## Candidate minimal acceptance checklist

- [ ] GoProp declares a resolvable, pinned `boctulus/simplerest` dependency and the deployable artifact is identified.
- [ ] Composer dependency constraints are compatible with tested target PHP.
- [ ] All runtime `Boctulus\Simplerest\Core\*` classes come from the installed dependency.
- [ ] Core helper/constants/template include paths work without GoProp's core copy.
- [ ] Original GoProp `com` and local `app/Commands/` work, including application-specific `users` behavior.
- [ ] No auto-registration of external commands and no CLI collision.
- [ ] Relevant HTTP, auth, DB, CLI, financial and Linux CI checks pass.
- [ ] No unauthorized write to persistent databases; rollback procedure tested.
- [ ] Runtime baseline and post-cutover measurements recorded.
- [ ] Reusable changes flow through the SimpleRest canonical source, not copying between consumers.

## Follow-up, not a blocking prerequisite

Design and implement the [command modularity RFC](simplerest-cli-modularity-rfc.md): a single command per file, physical folders by group, package discovery through explicit opt-in, deterministic conflicts, optional overrides/extensions, project-specific DB migrations, lazy command loading and measurable cold/warm startup cost. Group/domain packages such as `simplerest-cli-users` or `goprop-users` are optional future choices based on reuse and independent versioning.

## Contributor workflow

Work on SimpleRest for generic behavior and on GoProp for application behavior. Use separate branches or worktrees for experiments; test the SimpleRest change as a GoProp dependency in CI. Avoid copying `src/framework` between repositories. Publish a tagged core update only after consumer gates pass. Later add ACVAL, iDataSync and other supported applications as compatibility consumers.

## Evidence references

- `boctulus/goprop`: `composer.json`, `config/constants.php`, `config/autoload.php`, `config/commands.php`, `app.php`, `com`, `src/framework/`.
- `boctulus/simplerest`: `composer.json`, `scripts/pack_framework.php`, `app/Commands/pack/BuildCommand.php`, `.packignore`, `.packinclude`, `src/framework/`.
- This document is a source-based architecture plan. Local working trees, generated distribution and Packagist release metadata still require audit.

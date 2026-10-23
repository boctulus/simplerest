# Composer release process

## Pack-first microcore release procedure (2026-10-23)

This section supersedes all historical references below to building Composer releases directly from the development checkout.

- **Canonical development:** D:\laragon\www\simplerest (Git master). All fixes and tests begin here; the HTTP microcore consists only of src/framework/.
- **Curated distribution:** D:\laragon\www\simplerest-pack is the intended PACK output/worktree. Run `php com pack build --profile=core` from the development checkout. This command deliberately bypasses the obsolete app.php bootstrap and reads only the selected immutable Git source commit (default HEAD). The output consists of composer.json, LICENSE, README.md and PHP files under src/framework/.
- **Safety:** PACK refuses dirty Git destinations and unexpected files. It never recursively cleans a worktree, deletes unrelated files, or changes Git tags/remotes. For an existing legacy/non-curated destination, first inspect its history and preserve all changes; establish a clean destination through an explicit, reviewed migration.
- **Composer snapshot:** `scripts/composer/build-core-source-snapshot.ps1` now requires `-CuratedDirectory` and copies ONLY the previously generated curated PACK. It verifies each file's Git blob against `-SourceCommit`, rejects unrelated files, and creates a local validation tag inside a fresh isolated directory. It cannot silently fall back to Git archive of the monorepo.
- **Publication is a separate action:** Review the resulting snapshot and manifest, run Composer/platform/microcore and consumer tests, and only then authorize a new immutable release tag and Packagist update. Neither PACK nor snapshot uploads to Packagist.
- **Legacy application:** The pre-microcore application/CLI remains an independent development concern. Its explicitly loaded historical classes in app/LegacyCore and archive/ are not part of the Composer microcore autoload or pack output. This temporary development bridge needs dedicated regression tests and eventual migration to properly versioned extension packages.
- **Consumer compatibility:** The core 3.0 release is incompatible with GoProp's current Composer pin until the application and required extensions are migrated and tested. Existing v2.0.2 tags are immutable.

Recommended verification before any release:

```powershell
cd D:\laragon\www\simplerest
php com pack build --profile=core --dry-run
php com pack build --profile=core --source-commit=<source-sha> --dest=D:\laragon\www\simplerest-pack
.\scripts\composer\build-core-source-snapshot.ps1 -CuratedDirectory D:\laragon\www\simplerest-pack -SourceCommit <source-sha> -OutputDirectory <fresh-disposable-directory> -Version 3.0.0
```

These are validation commands, not authorization to mutate a pre-existing dirty pack worktree or publish a release.

> The current scope and evidence are recorded in **Current scope and status**. The implementation record farther below is historical; its package inventory, FriendlyPOS status, and GoProp conclusions predate the 2026-10-23 scope reset.

## Current scope and status (2026-10-23)

This section supersedes older package and blocker statements below while preserving them as an audit trail.

| Disposition | Packages and action |
| --- | --- |
| Legacy GitHub identities | Preserve `boctulus/simplerest-pdf` (`v1.0.1`), `boctulus/simplerest-code-tools` (`v1.1.0`), `boctulus/simplerest-integrations` (`v1.0.0`), and `boctulus/simplerest-html-builders` (`v1.0.0`) with their tags unchanged. Their current READMEs point to the successor catalog. |
| Public extension catalog | The six successor repositories are public on GitHub with validated package trees on `main`. No successor version tags or Packagist entries have been created. |
| Package boundaries | Bootstrap HTML owns `Bt5Form`, `AdminLte`, and `BootstrapPaginator`; Web Inspection owns `DomCrawler` and CMS detection; API Clients owns Google Drive, WooCommerce, OneSignal, and Hugging Face. PHP class namespaces and core compatibility names remain intact. |
| Core release gate | Core `3.0.0` remains untagged. GoProp's full migration test is still pending; GoProp was not modified. Its consumer fixture now includes Bootstrap HTML and Web Inspection. |
| Private or test scope | Keep Basic ACL and Fine-Grained ACL private; keep Shopify Connector and Exchange Rate private; retain DummyAPI and Web Test as test packages. A full AdminLTE dashboard or app shell remains outside the public Bootstrap HTML package. |
| Removed from SimpleRest | FriendlyPOS, OpenFactura SDK, Zippy, and the unused CLI Test package. FriendlyPOS and OpenFactura are maintained with Haulmer in `sr-haulmer`; their old application tests are outside this release gate. No vendored code was restored. |

The six successor repositories were split from source commit `7cb44199e717e2485f0b1bee357ccaa402016412`. Each GitHub `main` points to the listed split commit, and each split tree hash matches its package directory at that source commit. The repositories were empty before publication; no tags were pushed.

| Package | GitHub `main` split commit | Verified package tree |
| --- | --- | --- |
| `boctulus/simplerest-fpdf` | `efc06d12c02909b801b9bc89a4d27219da9f7473` | `cbc6d32bb10455a7a5aa882059ccc1b54c8d6810` |
| `boctulus/simplerest-scaffolding` | `e4a347660b87acab2eca06b4046ab996fcb446b2` | `645f9047f324182594a33e710965de46b42e3f29` |
| `boctulus/simplerest-bootstrap-html` | `7732d273558e334a8ae2bbcddc57a10ca84233dd` | `91a4d7a12f65bd5a071b7c59cad24f7ea497b887` |
| `boctulus/simplerest-web-inspection` | `7182f6b9318eb6794b5f61329c17103835ef2c28` | `74cc232c2a208e44780fc0e06e1fad7bf8c8ebc4` |
| `boctulus/simplerest-api-clients` | `f0fe1a4f910642976d727f861c7764cc854afb9d` | `241ab0ebf82ad4fffd4b657a08c726a3c0c1c832` |
| `boctulus/llm-providers` | `593811387fdddb2d5e376cef72a95d4705b9add3` | `5a3cd4769d405dcde1a196a85828e1613c2e2a63` |

`boctulus/simplerest-fpdf` is the successor identity for the standalone PDF adapter. It contains `PdfBase` and requires `boctulus/simplerest: ^2.0 || ^3.0` plus `setasign/fpdf: ^1.8`. FPDF remains absent from the core's mandatory `require` set. `ProductPDF` contains Relmotor-specific content and remains application code; `InvoiceGenerator` renders HTML views and is not an FPDF renderer.

Moving public classes out of the core is incompatible with `v2.0.2`; keep that release unchanged and version the modular core as `3.0.0`. The successor package manifests accept both `^2.0` and `^3.0` while preserving the current PHP namespaces. No version tag has been created for a successor identity, and the core still has no `3.0.0` tag or release.

### Current verification

- All fourteen current Composer manifests passed `composer validate --strict --no-check-publish`; 102 changed PHP files passed `php -l`, `git diff --check` passed, and the documentation governance guard checked 19 canonical files. The PHPUnit suites were not run during this catalog change.
- Focused package tests were added for representative scaffolding generators, Bootstrap pagination, API client contracts, and current LLM defaults. These tests still need execution before a release tag is considered.
- The former LLM Providers README did explicitly document that streaming is unsupported. The prior audit shorthand that it did not document streaming was inaccurate. The README now records the offline test suite and links to live provider model and pricing documentation instead of fixed historical price claims.
- GoProp remains unmodified and the full migration test remains pending. The consumer fixture now expects both Bootstrap HTML and Web Inspection. No result from that fixture or from GoProp's application tests is claimed here.
- The overrides load, but they do not match the candidate core's public method set: GoProp's `ApiController` lacks `hasUnrestrictedReadPermission()` and `getWebhookPublisher()`; its `Collections` and `Response` implementations also differ. Its `AuthController` contains GoProp-specific impersonation methods. This autoload smoke does not prove login, impersonation, ACL, REST, HTTP response, or database parity. Those differences need owner review and application-level tests before GoProp is called compatible.
- Focused root tests requiring `app.php` could not load because this checkout has no `.env`; no environment file was created or retained. The full root suite and database-backed tests remain unverified. Hosted PHP 8.1–8.4 CI passed, but it intentionally runs the isolated core/ACL test set rather than the `.env`-dependent application suite.

### Repository extraction and remaining blockers

The tested extraction fallback in `scripts/composer/split-existing-package.ps1` uses `git filter-repo` inside a disposable shared clone. It avoids checking out the monorepo's overlong historical filenames, filters only the requested package path, verifies the resulting root tree hash matches the source package tree, and strictly validates the filtered Composer manifest. By default, it changes no reference or file in the SimpleRest repository. The optional `-PersistSplit` switch fetches the validated commit into `refs/package-splits/<vendor>/<package>` without changing a branch or tag; this keeps the commit available for a later fast-forward push. The package trees below were split from source commit `3bf45ea5658afd30cd4165c4882e15520797e73d`:

| Package | Source tree | Filtered split HEAD | GitHub main / release tag |
| --- | --- | --- |
| `boctulus/simplerest-pdf` | `3ce98d1f1bdc1c2c69e065ed1275c4a63f2c43bb` | `6f3a9be747c900a18d2dd0c9b51ae6fda58af0ed` | `cc7b2c169aed09894363db46faf15a8c3b542700` / `v1.0.1` |
| `boctulus/simplerest-code-tools` | `9dfb72c3130add34de305587c8d705066573c72a` | `46949f9236cd9c67f6ba1f54e351c419d5206a3f` | `8bf814b226d3437531ca9d878d4b43c162df84be` / `v1.1.0` |
| `boctulus/simplerest-integrations` | `c4189dee46fd27901c2114401c8f995aae4b329f` | `1ca2f17b4f6cf7d6e4269d646eba717a7fdab7f5` | `1ca2f17b4f6cf7d6e4269d646eba717a7fdab7f5` / `v1.0.0` |
| `boctulus/simplerest-html-builders` | `a1647b2442fbcdf620f794b40afcdac88362550e` | `9fe259bda961cab130782dab6e14fa8dcb92c17e` | `9fe259bda961cab130782dab6e14fa8dcb92c17e` / `v1.0.0` |

Each filtered manifest passed `composer validate --strict --no-check-publish`; every root tree hash matches its source package tree. PDF and Code Tools each had one remote-only commit and no remote-only paths. A normal merge reported content conflicts in overlapping files, so their release commits preserve both histories as parents while using the exact tested package tree. Both pushes were fast-forward updates; no existing tag was changed. Integrations and HTML Builders were created as new public repositories and pushed with their initial immutable tags. The Packagist index has not been created for any of these four names.

Packagist's create-package API requires the account's main API token. None is configured in this environment. After the maintainer configures that credential, register the four existing GitHub repositories and verify each package through a Packagist-only consumer install. The Composer VCS and local consumer checks above do not count as Packagist registration.

Before publishing SimpleRest 3.0, the remaining work is:

1. Run the GoProp application suite separately against SimpleRest `3.0.0` with HTML Builders installed. Review the five local overrides and keep GoProp's `dev` branch unchanged during the trial.
2. Decide how to execute the `.env`-dependent and database-backed root suite in an isolated environment; the full suite has not been verified.
3. Keep the core `3.0.0` tag and release pending until the consumer compatibility gates above are reviewed. The four package tags can be released independently and do not certify the core release.

LLM Providers can wait without blocking the framework. Keep these pending core gates visible in any P0/P1 status decision.

## Source of truth

`D:\laragon\www\simplerest` is the canonical development repository. `D:\laragon\www\simplerest-pack` is a legacy generated application tree; it is not the Composer source and is never an automatically cleaned release destination.

The root package name remains `boctulus/simplerest`. Packagist consumes this GitHub repository; `php com pack build` creates a starter application in `simplerest-pack`, not the Composer library. A core release is built from an immutable commit of this repository. `.gitattributes` controls the GitHub archive used for Composer `dist`; it does not change a source checkout.

The optional Composer packages `boctulus/simplerest-pdf` and `boctulus/simplerest-code-tools` use their existing repositories and are split from their monorepo directories with `git subtree split`. The new local candidate `boctulus/simplerest-integrations` uses the same reproducible split route, but has no independent repository or Packagist entry yet. A split commit is deterministic for a given source commit and path. The maintainer must record both SHAs. The script defaults to a dry run and refuses to force-update branches or reuse a version tag.

The existing `boctulus/basic-acl` and `boctulus/fine-grained-acl` package manifests are proprietary. This pipeline does not create public repositories or Packagist entries for them.

As of 2026-10-22, the existing public GitHub repositories for Code Tools and PDF resolve and each has a `v1.0.0` tag. Their remote tag trees exactly match their corresponding package directories at SimpleRest commit `74ac9ea59a637670ed53552443aad96bc0e1c3af` (46 and 3 blobs). A clean consumer installed both tags directly through Composer VCS and resolved the core from Packagist; optimized autoload, platform checks, runtime class loading, package command discovery, and a host/package collision smoke passed. The Packagist P2 endpoint still returns 404 for Code Tools and PDF, so default Packagist-only installation remains unavailable. The existing Basic ACL and Fine-Grained ACL names are proprietary local packages; do not create duplicate identities or publish them through this pipeline.

The VCS consumer used PHP 8.2.35 and locked `boctulus/simplerest` v2.0.1 at `9ed4141dd84639799a4220ea58e4ebdaae0f4d3f`, `boctulus/simplerest-code-tools` v1.0.0 at `8806fcb4447aece9968bc83e3032168b38184267`, and `boctulus/simplerest-pdf` v1.0.0 at `4a056092ea67c511d7805e1aeba0274064d47881`. Installed package roots measured 273 files/1,600,081 bytes, 46 files/195,312 bytes, and 3 files/5,068 bytes respectively, with zero frontend asset files in those roots. The optional upstream FPDF dependency contributes its own CSS and character-map support files in `vendor/`; it is not bundled into the SimpleRest package roots.

Before the final tag, core commit `74ac9ea59a637670ed53552443aad96bc0e1c3af` passed a candidate VCS consumer install under temporary branch alias `2.0.2` on PHP 8.2.35. Core and optional package classes loaded from their Composer paths, `make:console` was discovered, platform checks passed, and focused schema/upload tests passed 12/12 tests and 34 assertions. This was pre-release evidence; the immutable-tag install is recorded below.

On 2026-10-22, `build-core-source-snapshot.ps1` assembled package commit `c15d0627afeae9d6bdd0067ee917b8ec7c043d41` from SimpleRest source commit `b7a48ac6d1c2c7bf99794b3e7d5ad6a70742c238`. The immutable `v2.0.2` tag is published on GitHub and Packagist metadata resolves the same reference. The exact public GitHub ZIP contains 273 files, 475,250 compressed bytes and 1,604,399 expanded bytes; inspection found no frontend assets or third-party UI bundle paths. Clean tagged consumers passed `--no-dev --prefer-dist --optimize-autoloader` and `composer check-platform-reqs` with Composer 2.8.5 on PHP 8.1.14, 8.2.35 and 8.3.36. A separate `--prefer-source` consumer also resolved `v2.0.2`; its checked-out package tree contains 273 files and no frontend assets (1,658,610 bytes on Windows with CRLF checkout), while `.git` adds 568,459,924 bytes and contains historical asset paths for jQuery, Font Awesome, and SweetAlert. The `v2.0.2` checked-out tree is asset-free, but source mode retains historical assets in Git objects. A truly lean source install would need an independent source repository; that would not require a new Composer package identity.

## Release gates

1. Inspect `git status` and select a committed source SHA. Do not include uncommitted files or modify user changes.
2. Run `composer validate --strict --no-check-publish` for the root and every package manifest.
3. Run focused tests for core behavior, command registration, collisions, and optional integrations. Database tests require a separately verified disposable database.
4. Produce a root archive from the candidate commit and record its file count, compressed size, and expanded size. Inspect the archive entries and fail the release if it contains frontend assets (`.js`, `.css`, source maps, fonts, or frontend images) or paths/names for third-party UI libraries such as jQuery, Tailwind, Bootstrap, Font Awesome, DataTables, or SweetAlert. The exact Packagist `v1.0.3` ZIP at `e686ceee139f69c444c45a3c4f941842440f8a4f` contained 10,175 files, 4,263 frontend assets and 981 common third-party path/name matches (430,530,282 compressed bytes; 669,324,750 expanded bytes). Candidate `439a18044` measures 273 files, zero frontend assets, 460,895 compressed bytes, and 1,654,024 expanded bytes. Treat this as an archive candidate until a tagged VCS/Packagist consumer install confirms the published artifact.
5. For each public optional package, run `scripts/composer/split-existing-package.ps1` without `-Push`; inspect the emitted package commit and package manifest.
6. Install the root package and selected runtime packages in an empty temporary Composer consumer with `--no-dev --prefer-dist --optimize-autoloader`. In a separate development install, enable Code Tools and verify command discovery, precedence, aliases, overrides, and class provenance. Verify the core's HTTP request/response smoke behavior in both consumers.
7. Run the same consumer with source installation when available. Report source-repository size separately; `.gitattributes` does not filter a Git clone.
8. Only after the consumer gates pass, publish new immutable versions. Never reuse `v1.0.3`, and never force-push tags or release branches.
9. Register the two existing optional package names on Packagist against their split GitHub repositories, then run a second consumer install using Packagist only. Record the exact lockfile references and `composer show --locked` output. Until this gate passes, path-repository checks are preflight evidence only.

## Frontend asset boundary

Composer framework and tooling libraries may contain manifests and documentation, but must not bundle frontend distributions. jQuery, Tailwind, Bootstrap, and similar third-party frontend assets remain owned by the host application; they are not installed, copied, or bundled by the SimpleRest core or its Code Tools, PDF, and Integrations packages. Assets under the development app's `app/` and `public/` paths are excluded from the root package archive.

The existing `boctulus/simplerest` package identity is released from a reproducible, lean source snapshot assembled from the root manifest and `src/framework/`. This keeps the package's Composer name and avoids adding a second core entry to any package list. `build-core-source-snapshot.ps1` requires a new verified temporary output directory, creates a local validation tag there, and fails if frontend files are present. It does not change a remote or any published tag. This clean snapshot is required for `--prefer-source`; `.gitattributes export-ignore` alone only filters archives, not Git checkouts.

## Core versioning

The current published core version is `v2.0.2`; version 2 removed two optional classes from the core provider paths and changed runtime dependency requirements. Preserve `v1.0.3` and its existing source provenance. Future releases require a new immutable tag and a clean tagged consumer install. Each release record must state the monorepo source commit, package commit, tag, Packagist reference, archive metrics, PHP runtime, Composer version, and consumer lock reference.

## Optional package split

Example dry run:

```powershell
.\scripts\composer\split-existing-package.ps1 `
  -Package boctulus/simplerest-code-tools `
  -SourceCommit <40-character-source-sha>
```

After review, run the same command with `-Push -Version <new-immutable-version>` only after verifying that the version does not already exist. The target repository must already exist and contain the same package root manifest name. A failed fast-forward or existing tag is a release blocker, not a reason to force an update.

## Implementation record: distribution and modularization (2026-10-23)

This record describes an unpublished candidate on branch `fix/composer-distribution-modularization-recovered-20261022`. The code changes are based on source commit `aaafd81bcbe0b099b2d801cfbdf6d2740fa42f50` and are present through commit `297064ff96b7b983fcee5ae5bbccc9b15f11dc12`. No release, tag, Packagist registration, push, deployment, or GoProp change was made. The published `v2.0.2` history above remains unchanged; the candidate manifests and artifacts below are not yet public.

### P0 distribution boundary

Composer core archives, extension archives, and the generated starter are different products and now have separate checks. `.gitattributes` excludes Git metadata from Composer archives. `.packignore`, `.packinclude`, and `scripts/pack_framework.php` preserve the starter's first-party bootstrap and assets while refusing `third_party`, even when an include rule would otherwise match it. The starter no longer depends on copied Bootstrap files or application views from the development checkout; its first-party welcome view is under `src/framework/Templates/FreshCopy/app/Views/home.php`.

`packages/boctulus/friendlypos-web/third_party/` no longer contains the 495 copied QR Code and XLSX files. A source scan found no imports or runtime calls to those copies, so no replacement runtime dependency was added. If a later FriendlyPOS feature uses one of those libraries, its Composer requirement must be declared in that package. `friendlypos-web` remains proprietary and depends on `boctulus/openfactura-sdk`; it is not a public release candidate.

The new gate, `scripts/distribution/verify-release-artifacts.php`, examines Composer-produced ZIPs for the core and all 15 local packages plus the generated starter. It rejects vendored directories, known third-party library signatures and namespaces, and frontend distributions, while allowing first-party framework files that import managed dependencies. `--self-test` verifies six rejected fixtures, two allowed framework fixtures, and the `.git` safety guard. `--with-consumer-install` creates temporary VCS repositories from the exact ZIPs, installs all local packages with the core in a clean Composer consumer, checks platform requirements and autoload smoke paths, and separately installs PDF, Code Tools, and Integrations from VCS against the stable Packagist core. These checks do not use a Composer `path` repository.

| Artifact | Candidate result | Previous published core |
| --- | ---: | ---: |
| Core Composer ZIP | 274 files; 436,273 compressed bytes; 1,597,277 expanded bytes | `v2.0.2`: 273 files; 475,250 compressed bytes; 1,604,399 expanded bytes |
| Generated starter | 457 files; 5,248,899 expanded bytes | Not a Composer core artifact |
| `friendlypos-web` ZIP | 101 files; 292,184 expanded bytes | Before removal, 495 copied third-party files occupied its `third_party/` tree |
| `simplerest-integrations` ZIP | 20 files; 18,454 compressed bytes; 49,325 expanded bytes | New local candidate; not published |
| Third-party or frontend payload detected by the gate | 0 in core, extension ZIPs, and starter | — |

The core archive is 36,777 bytes smaller than the earlier unpublished candidate and 7,122 bytes smaller than the published `v2.0.2` archive. The starter is 36,969 bytes smaller than the earlier candidate. These size changes come from moving optional implementations out of the core; the stronger architectural evidence is that copied third-party payloads are rejected, the starter and Composer release have distinct rules, and three optional packages have standalone Composer contracts. The starter still contains the first-party application scaffold needed to be usable.

### Core inventory and dependency boundary

The PHP AST inventory in `docs/audit/simplerest-core-class-inventory-20261023.csv` covers 270 PHP files under `src/framework/` and 226 named classes, interfaces, traits, or enums. Those declarations comprise 201 `CORE`, 2 `OPTIONAL`, and 23 `INTERNAL` symbols. Sixteen compatibility shims are separate `LEGACY` entries: two cleaner aliases and 14 integration aliases, for 242 inventory rows total.

| Category | Decision and current scope |
| --- | --- |
| `CORE` | Keep HTTP, Request/Response, routing, middleware, configuration/bootstrap, controllers and REST API, automatic endpoints, DB/Schema/Query Builder, essential security/authentication, and extension contracts. These responsibilities were not split. |
| `OPTIONAL` | `AngularCleaner` and `BootstrapCleaner` implementations moved to `boctulus/simplerest-code-tools`; PDF stays in `boctulus/simplerest-pdf`. `BootstrapPaginator`, `CMS_Scanner`, `DomCrawler`, Google Drive, WooCommerce, Hugging Face, and OneSignal implementations moved to `boctulus/simplerest-integrations`. `Bt5Form` and `AdminLte` remain in core; GoProp directly uses `Bt5Form`. |
| `INTERNAL` | Generated starter templates are scaffolding, not runtime extension APIs. |
| `LEGACY` | The former cleaner FQCNs resolve to Code Tools when it is installed. The 14 former integration FQCNs resolve to the Integrations package when it is installed. Each shim throws a `LogicException` with the installation direction if its extension is absent. |

A read-only search of GoProp's `dev` branch at `40e0548c917fcf2b157eabc5a3713f415854dbe9`, outside its bundled framework, found a direct use of `Bt5Form` in `app/Views/userlogin/login.php` and no direct references to the extracted integration classes. The detailed compatibility review is recorded below. The published core `v2.0.2` still contains the original implementations; compatibility shims apply only to this unpublished core candidate. No runtime GoProp test was run and GoProp was not modified.

The candidate core manifest now requires PHP `>=8.1,<8.5`, `ext-pdo`, `ext-mbstring`, and three Composer packages: `doctrine/inflector`, `psr/http-message`, and `firebase/php-jwt`. Its lockfile has zero additional transitive production packages for those requirements. The baseline manifest required five Composer packages and locked three additional production transitives (eight runtime packages total). Symfony DomCrawler and CSS Selector remain in the monorepo root's `require-dev` for local tests; the Integrations extension declares them as runtime requirements. Google API Client is suggested by Integrations only for consumers that use Google Drive. The published `v2.0.2` manifest still has its old requirements until a future core release.

| Package | Mandatory Composer requirements | Optional third-party requirements |
| --- | --- | --- |
| `boctulus/simplerest` core candidate | `doctrine/inflector ^2.0`, `psr/http-message ^2.0`, `firebase/php-jwt ^7.1`; PHP `>=8.1,<8.5`, `ext-pdo`, `ext-mbstring` | None in the runtime manifest; the candidate lock contains 3 runtime packages and 0 transitive packages. |
| `boctulus/simplerest-pdf` | Core `^2.0`, `setasign/fpdf ^1.8` | FPDF is resolved by Composer; its source is not copied into the package. |
| `boctulus/simplerest-code-tools` | Core `^2.0`, `nikic/php-parser ^5.4` | None. |
| `boctulus/simplerest-integrations` | Core `^2.0`, `symfony/css-selector ^6.4`, `symfony/dom-crawler ^6.4`, `ext-curl` | `google/apiclient` is suggested only for consumers that use Google Drive. |

The other 12 local packages also declare `boctulus/simplerest ^2.0`; `friendlypos-web` additionally requires `boctulus/openfactura-sdk ^1.0`. Their third-party PHP dependencies are represented by those package manifests, not copied under `third_party/` or `vendor/`.

All 15 local package manifests now declare their PHP range, license, real core or package requirements, PSR-4/provider metadata, and development requirements where applicable. The release gate runs `composer validate --strict --no-check-publish` on the root and all 15 manifests.

### Local package inventory and publication status

| Package | Classification | Decision |
| --- | --- | --- |
| `boctulus/az-seeder` | Test-data seeder | Development/support package; not a public consumer extension. |
| `boctulus/basic-acl` | Framework ACL extension | Proprietary; keep private. |
| `boctulus/cli-test` | CLI testing utility | Development-only; not a public runtime package. |
| `boctulus/dummyapi` | Example/test API | Fixture package; not a public runtime package. |
| `boctulus/exchange-rate` | Reusable integration candidate | Manifest/provider corrected; requires focused tests, consumer documentation, and isolated install review. |
| `boctulus/fine-grained-acl` | Framework ACL extension | Proprietary; keep private. |
| `boctulus/friendlypos-web` | Product/application integration | Proprietary; copied third-party code removed; not publishable as a public package. |
| `boctulus/llm-providers` | Reusable AI integration candidate | Core dependency declared; requires focused tests and consumer documentation. |
| `boctulus/openfactura-sdk` | Framework-coupled SDK | Core dependency and provider metadata declared; requires isolated tests and release review. |
| `boctulus/shopifyconnector` | Reusable external integration candidate | PSR-4 metadata corrected; requires isolated tests and consumer documentation. |
| `boctulus/simplerest-code-tools` | Optional development extension | Standalone Composer install, optimized autoload, command discovery, and 4 package tests (11 assertions) pass against stable core `v2.0.2`; test-ready for split review. |
| `boctulus/simplerest-integrations` | Optional external/UI integrations | 14 optional implementations moved out of core; Composer VCS install, optimized autoload, and 5 package tests (15 assertions) pass against stable core `v2.0.2`; test-ready for split review. Google API Client is optional within this package. |
| `boctulus/simplerest-pdf` | Optional PDF extension | Core constraint updated to `^2.0`; standalone Composer install, optimized autoload, and 1 package test (2 assertions) pass against stable core `v2.0.2`; test-ready for split review. |
| `boctulus/web-test` | Web testing utility | Development-only; not a public runtime package. |
| `boctulus/zippy` | Product-specific application integration | Proprietary; keep private. |

The repository now has 15 local subpackage manifests. All 15 were packaged and installed together from temporary VCS repositories in the consumer gate; PDF, Code Tools, and Integrations were also installed and tested as independent candidates. The last Packagist count verified in the 2026-10-22 audit was one package (`boctulus/simplerest`); this task did not requery Packagist and did not push, tag, or register a subpackage. Three packages are ready for split review; none is release-ready until the split commit and repository root are verified.

### Verification and remaining release gates

On PHP 8.3.36 with Composer 2.8.5, the current candidate passed:

- the distribution detector self-test;
- strict Composer validation for the root and all 15 package manifests;
- artifact scans for the core, all package ZIPs, and the starter;
- a clean VCS consumer install of the core plus all 15 local packages, including optimized package autoload smoke paths and platform checks;
- standalone PDF, Code Tools, and Integrations consumers against the stable Packagist core `v2.0.2`, including each package's test suite;
- focused root Composer/CSS tests: 5 tests and 23 assertions;
- standalone package-root installs and tests for PDF (1 test, 2 assertions), Code Tools (4 tests, 11 assertions), and Integrations (5 tests, 15 assertions);
- the documentation governance guard.

CI now runs the focused tests and independent extension suites on PHP 8.1, 8.2, 8.3, and 8.4. The hosted matrix has not been run as part of this work; only PHP 8.3.36 was available locally. The full root PHPUnit suite was not run. Network behavior of external integrations and runtime compatibility with GoProp remain follow-up checks.

The reproducible publication route remains `scripts/composer/split-existing-package.ps1` with an explicit source commit and no `-Push` for review. A dry run was attempted at source commit `297064ff96b7b983fcee5ae5bbccc9b15f11dc12`, but the local command remained in the repository-wide history traversal of 5,325 commits and was stopped before emitting split SHAs. It created no branch refs and made no remote changes. The gate's temporary VCS repositories were built from the exact Composer ZIP artifacts, so standalone Composer behavior is verified; the actual split SHAs still need to be produced and checked as part of release review. After reviewing the split commit and confirming each package's repository root manifest, a maintainer can publish an immutable new version and register or update the existing Packagist entry. Do not reuse an existing tag. Until the core candidate itself is released, Composer-only consumers still receive the dependency metadata of published `v2.0.2`.

## Follow-up audit: modular compatibility and consumer gates (2026-10-23)

This follow-up was run on local branch `fix/composer-distribution-modularization-recovered-20261022`, source `bb840914493b5aa59c3613359e67574c07cf85a0`, with audit changes present in the worktree. It did not push, merge, tag, release, or publish. Tests and filtering ran in isolated Composer roots or disposable clones. The unrelated pre-existing edit to `app/Views/templates/adminlte_tpl.php` was preserved.

### Extracted integrations and SemVer

The 14 extracted implementations are `BootstrapPaginator`; `CMS_Scanner\Scanner`; the `Magento`, `PrestaShop`, `Shopify`, `Squarespace`, `Wix`, and `WordPress` CMS detectors; `DomCrawler`; `GoogleDrive`; `HuggingFace`; `OneSignal`; `WooCommerce`; and `WooCommerceApiClient`. The new contract suite checks both the new `Boctulus\Simplerest\Integrations\Libs\...` names and the former `Boctulus\Simplerest\Core\Libs\...` names, paths, key methods, detector recognition, crawler helpers, pagination output, and offline client behavior.

An optimized Composer loader with `classmap-authoritative` did not discover the conditional `class_alias()` compatibility files. The candidate now loads `src/framework/Compatibility/OptionalPackageClassAliases.php` through Composer `autoload.files` and uses it as a fallback for the 14 integration names and two Code Tools cleaner names. Isolated consumers with the extension installed resolve both names under authoritative autoload. With only core `v2.0.2`, the former names resolve to the original core classes. With only the candidate core, the new names stay absent and requests for former names fail with a `LogicException` directing the consumer to the required optional package. The optional Google API SDK remains absent in the integration test environment; `GoogleDrive` reports its missing `google/apiclient` dependency clearly.

`class_alias()` preserves common source references and instantiation, but it does not preserve every observable class identity: reflection reports the new implementation FQCN, and code that compares `get_class()` exactly or persists serialized class names can observe the move. Deployments that previously used these classes also need the new Composer package. Removing implementations from a published core is therefore a breaking change from `v2.0.2`; the correct core release line is `3.0.0`, not another `2.x`. Keep `v2.0.2` available unchanged and document the optional package requirements. The candidate packages audited here accept `^2.0 || ^3.0`; ten other local package manifests still require only `^2.0`: `az-seeder`, `basic-acl`, `cli-test`, `dummyapi`, `exchange-rate`, `fine-grained-acl`, `llm-providers`, `shopifyconnector`, `web-test`, and `zippy`. Those ten packages are not yet compatible with or tested against core `3.0.0`.

The isolated Composer matrix enabled optimized autoload and authoritative classmaps, with a distinct `vendor/` per core version to avoid class collisions. Integrations passed 12 tests / 212 assertions with `v2.0.2` and 12 / 240 with the candidate; PDF passed 1 / 2 on each core; Code Tools passed 4 / 11 on each core. The assertion difference for Integrations reflects the different class identities of the concrete classes in `v2.0.2` and their aliases in the candidate. These tests cover offline contracts; they do not prove live provider or network behavior.

The package runtime requirements are explicit: Integrations requires PHP `>=8.1,<8.5`, `ext-curl`, Symfony DomCrawler `^6.4`, and CSS Selector `^6.4`; Google API Client is optional and suggested only for Google Drive. PDF requires `setasign/fpdf ^1.8`; Code Tools requires `nikic/php-parser ^5.4`. The core candidate itself keeps its mandatory runtime set to PHP `>=8.1,<8.5`, `ext-pdo`, `ext-mbstring`, `doctrine/inflector`, `psr/http-message`, and `firebase/php-jwt`; cURL, Symfony crawler components, FPDF, and PHP Parser are not core runtime requirements.

### FriendlyPOS after `third_party/` removal

`packages/boctulus/friendlypos-web/third_party/` is absent. A PHP source/config scan found no active import or invocation of the removed QR Code and XLSX copies, and no FPDF class instantiation; the remaining FPDF config file only contains configuration keys. Mentions in the package README explain the removal, while `docs/PLAN-MIGRACION.md` contains older migration instructions and copy paths. No vendored replacement was added.

The OpenFactura SDK calls SimpleRest `ApiClient`, which uses cURL. `ext-curl` is now declared by `boctulus/openfactura-sdk`, so FriendlyPOS obtains it as a transitive runtime requirement. Standalone path-repository consumers installed FriendlyPOS, the SDK, and core `v2.0.2` and the candidate core; platform checks and autoload smoke checks for `OpenFacturaController`, `OpenFacturaSDK`, and `ApiClient` passed on both. These were local Composer installs, not public VCS/tag installs.

The available FriendlyPOS PHPUnit run is not green: 40 tests, 49 assertions, 6 errors, 3 failures, 1 PHPUnit runner warning, and 3 risky tests. Six errors concern attempts to mock the static `Request::getHeaders()` method; three controller expectations receive HTTP 500 where the tests expect 400. PHPUnit also reports that `DteDataTransformerTest.php` has no test class. No root `.env` existed; a temporary empty `.env` was created for this run and removed afterward. These failures are not evidence that QR or XLSX functionality was removed from an active caller, but they prevent a claim that the full FriendlyPOS contract is proven intact. No provider-facing live request was made.

### Repository extraction procedure

`git subtree split` was attempted only in a disposable clone. The no-checkout invocation reported that the prefix did not exist; after a detached checkout, Windows rejected several long documentation filenames, and the PDF split was still traversing the repository's 5,332 commits when stopped at approximately 829. No split SHA was produced. The original repository and its refs were not changed.

As a fallback, `git filter-repo` ran successfully in three separate disposable shared clones, one per package. It filtered the package prefix to repository root, rewrote only those clones, and produced a root `composer.json` in each result. The rewritten histories contain 7 commits for PDF, 7 for Code Tools, and 1 for Integrations. Their temporary HEADs were `31166c6fb987e8189904880bb4c4204df79ed278`, `2683d9c07975acc819767a34a9e4355850673584`, and `369a69f7db47c29773147584ba0510acc4f4af8e`, respectively. All three filtered root manifests pass `composer validate --strict --no-check-publish`. These are extraction-procedure results from committed source `bb840914`; they exclude the uncommitted manifest edits from this audit and are not release SHAs. Recompute the splits from the final reviewed source commit.

### GoProp compatibility review (read-only)

GoProp was reviewed at `D:\laragon\www\goprop`, branch `dev`, commit `40e0548c917fcf2b157eabc5a3713f415854dbe9`. Its worktree already had a modified Svelte settings file; the review did not change it. The root `composer.json` requires `boctulus/simplerest` as `dev-codex/simple-rest-composer-convergence-20261022 as 2.0.x-dev`, and `composer.lock` resolves `3af664094a8ba7055adc36db97a79f18e354fa34`. This requirement cannot resolve a future core `3.0.0` without a manifest and lock update.

A static search of tracked `app/`, `packages/`, `tests/`, and `unit-tests/` on `dev` found no use of the 14 extracted Integrations classes or the two Code Tools cleaners. The only direct reference among the moved symbols is `Bt5Form`, imported and called by `app/Views/userlogin/login.php`. `Bt5Form` remains in the SimpleRest core, so this use does not require Integrations or Code Tools.

After `3.0.0` is released, GoProp will need its core constraint changed to `^3.0` and its lockfile updated to the immutable release. The search gives no reason to add `boctulus/simplerest-integrations` or migrate the `Bt5Form` call. This is static compatibility evidence only: no GoProp Composer install or runtime tests against the unreleased candidate were run, and GoProp was not modified.

### Distribution and remaining blockers

The distribution gate self-test passed six rejected fixtures, two allowed framework fixtures, and the `.git` marker guard. The full gate strictly validated the root and all 15 package manifests and scanned Composer archives for the core, all packages, and the generated starter. It passed with no copied third-party or frontend payload detected. The candidate core archive contains 275 files; Integrations contains 22. This confirms the gate still rejects copied libraries for the reviewed artifacts.

P0/P1 are not closed. Before release, the maintainer must approve the core `3.0.0` versioning decision and release metadata; the FriendlyPOS suite needs its failures resolved or explicitly dispositioned and rerun; the ten `^2.0`-only packages need an explicit compatibility decision and consumer evidence; GoProp still needs its `2.0.x-dev` alias changed to `^3.0` and a consumer install/runtime check against the released core. The GoProp source review found no direct use of extracted Integrations or Code Tools classes beyond `Bt5Form`, which remains in core. The full root PHPUnit suite was not run because it includes database-backed and HTTP/API tests and no safe disposable service fixtures were established. Hosted PHP 8.1-8.4 CI, live provider requests, and final split SHAs have not been verified. Do not claim P0/P1 closure while these gates remain open.

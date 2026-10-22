# Composer release process

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

The new gate, `scripts/distribution/verify-release-artifacts.php`, examines Composer-produced ZIPs for the core and all 15 local packages plus the generated starter. It rejects vendored directories, known third-party library signatures, and frontend distributions, while allowing first-party framework files. `--self-test` verifies four rejected fixtures, one allowed framework fixture, and the `.git` safety guard. `--with-consumer-install` creates temporary VCS repositories from the exact ZIPs, installs all local packages with the core in a clean Composer consumer, checks platform requirements and autoload smoke paths, and separately installs PDF, Code Tools, and Integrations from VCS against the stable Packagist core. These checks do not use a Composer `path` repository.

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

A read-only search of GoProp PHP outside its bundled framework found a direct use of `Bt5Form` in `app/Views/userlogin/login.php` and no direct references to the extracted integration classes. This supports retaining the UI builder but does not prove that other external consumers do not use the remaining public classes. The published core `v2.0.2` still contains the original implementations; compatibility shims apply only to this unpublished core candidate. No runtime GoProp test was run and GoProp was not modified.

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

The repository now has 15 local subpackage manifests. All 15 were packaged and installed together from temporary VCS repositories in the consumer gate; PDF, Code Tools, and Integrations were also installed and tested as independent candidates. The public package count remains one (`boctulus/simplerest`): no subpackage was pushed, tagged, or registered on Packagist. Three packages are ready for split review; none is release-ready until the split commit and repository root are verified.

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

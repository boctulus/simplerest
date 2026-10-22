# Composer release process

## Source of truth

`D:\laragon\www\simplerest` is the canonical development repository. `D:\laragon\www\simplerest-pack` is a legacy generated application tree; it is not the Composer source and is never an automatically cleaned release destination.

The root package name remains `boctulus/simplerest`. Packagist consumes this GitHub repository; `php com pack build` creates a starter application in `simplerest-pack`, not the Composer library. A core release is built from an immutable commit of this repository. `.gitattributes` controls the GitHub archive used for Composer `dist`; it does not change a source checkout.

The existing optional Composer names `boctulus/simplerest-pdf` and `boctulus/simplerest-code-tools` are split from their existing directories using `git subtree split`. The split commit is deterministic for a given source commit and path. The maintainer must record both SHAs. The script defaults to a dry run and refuses to force-update branches or reuse a version tag.

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

Composer framework and tooling libraries may contain manifests and documentation, but must not bundle frontend distributions. jQuery, Tailwind, Bootstrap, and similar third-party frontend assets remain owned by the host application; they are not installed, copied, or bundled by the SimpleRest core or its Code Tools/PDF packages. Assets under the development app's `app/` and `public/` paths are excluded from the root package archive.

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

After review, run the same command with `-Push -Version v1.0.0`. The target repository must already exist and contain the same package root manifest name. A failed fast-forward or existing tag is a release blocker, not a reason to force an update.

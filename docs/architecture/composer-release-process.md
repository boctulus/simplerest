# Composer release process

## Source of truth

`D:\laragon\www\simplerest` is the canonical development repository. `D:\laragon\www\simplerest-pack` is a legacy generated application tree; it is not the Composer source and is never an automatically cleaned release destination.

The root package name remains `boctulus/simplerest`. Packagist consumes this GitHub repository; `php com pack build` creates a starter application in `simplerest-pack`, not the Composer library. A core release is built from an immutable commit of this repository. `.gitattributes` controls the GitHub archive used for Composer `dist`; it does not change a source checkout.

The existing optional Composer names `boctulus/simplerest-pdf` and `boctulus/simplerest-code-tools` are split from their existing directories using `git subtree split`. The split commit is deterministic for a given source commit and path. The maintainer must record both SHAs. The script defaults to a dry run and refuses to force-update branches or reuse a version tag.

The existing `boctulus/basic-acl` and `boctulus/fine-grained-acl` package manifests are proprietary. This pipeline does not create public repositories or Packagist entries for them.

As of 2026-10-22, Packagist returns 404 for the existing Code Tools, PDF, Basic ACL, and Fine-Grained ACL package names. The current GitHub account also cannot resolve the Code Tools and PDF split repository names. These names and local package directories remain the intended identities; do not create duplicates. No split release or Packagist install is complete until its existing target repository is available and a clean consumer resolves it.

## Release gates

1. Inspect `git status` and select a committed source SHA. Do not include uncommitted files or modify user changes.
2. Run `composer validate --strict --no-check-publish` for the root and every package manifest.
3. Run focused tests for core behavior, command registration, collisions, and optional integrations. Database tests require a separately verified disposable database.
4. Produce a root archive from the candidate commit and record its file count, compressed size, and expanded size. The 2026-10-22 baseline is the exact Packagist `v1.0.3` ZIP at `e686ceee139f69c444c45a3c4f941842440f8a4f` (10,175 files, 430,530,282 compressed bytes, 669,324,750 expanded bytes). Candidate `f7d098822` measures 273 files, 460,895 compressed bytes, and 1,654,024 expanded bytes. Treat this as an archive candidate until a tagged VCS/Packagist consumer install confirms the published artifact.
5. For each public optional package, run `scripts/composer/split-existing-package.ps1` without `-Push`; inspect the emitted package commit and package manifest.
6. Install the root package and selected runtime packages in an empty temporary Composer consumer with `--no-dev --prefer-dist --optimize-autoloader`. In a separate development install, enable Code Tools and verify command discovery, precedence, aliases, overrides, and class provenance. Verify the core's HTTP request/response smoke behavior in both consumers.
7. Run the same consumer with source installation when available. Report source-repository size separately; `.gitattributes` does not filter a Git clone.
8. Only after the consumer gates pass, publish new immutable versions. Never reuse `v1.0.3`, and never force-push tags or release branches.
9. Register the two existing optional package names on Packagist against their split GitHub repositories, then run a second consumer install using Packagist only. Record the exact lockfile references and `composer show --locked` output. Until this gate passes, path-repository checks are preflight evidence only.

## Core versioning

The current core package removes two optional classes from the core provider paths and changes runtime dependency requirements. Release it as a new major version (`2.0.0`) after validation. Preserve `v1.0.3` and its existing source provenance. The final release record must state the monorepo commit, tag, Packagist reference, archive metrics, PHP runtime, Composer version, and consumer lock reference.

## Optional package split

Example dry run:

```powershell
.\scripts\composer\split-existing-package.ps1 `
  -Package boctulus/simplerest-code-tools `
  -SourceCommit <40-character-source-sha>
```

After review, run the same command with `-Push -Version v1.0.0`. The target repository must already exist and contain the same package root manifest name. A failed fast-forward or existing tag is a release blocker, not a reason to force an update.

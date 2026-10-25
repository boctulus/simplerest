# PACK hardening and distribution contract

## Purpose and authority

PACK is the official local curation operation for the SimpleRest Composer core. Its authoritative command is:

```powershell
php com pack build --profile=core
```

PACK reads an immutable commit from the canonical development repository at `D:\laragon\www\simplerest` and generates the curated tree at `D:\laragon\www\simplerest-pack` by default. It does not publish a release and does not establish what Packagist currently distributes.

The four distribution operations are separate:

| Operation | Responsibility | Must not do |
| --- | --- | --- |
| PACK | Generate the curated core tree from an immutable canonical commit. | Publish, tag, push, update Packagist, or modify an application. |
| VERIFY | Check curated bytes, external provenance, the snapshot repository, and an installed Composer candidate. | Authorize or publish a release. |
| PUBLISH | After explicit maintainer authorization, publish a new immutable package commit and tag and refresh Packagist. | Reuse or rewrite an existing public tag. |
| CONSUME | Update and test a specific application through Composer. | Run automatically because PACK or VERIFY succeeded. |

GoProp and other applications are never modified by PACK.

## Distribution topology

The canonical monorepo, curated destination, snapshot repository, and Packagist source have different roles:

1. `D:\laragon\www\simplerest` contains development source, PACK code, tests, documentation, applications, and packages.
2. `D:\laragon\www\simplerest-pack` is the supported curated PACK destination and may be its own Git worktree.
3. `scripts/composer/build-core-source-snapshot.ps1` verifies a curated tree against the approved canonical HEAD and creates a fresh standalone package repository plus an external `release-provenance.json` record.
4. Packagist installs source or dist bytes from the Git repository and commit registered for the selected version. It does not read the local PACK directory.

The published `v2.0.2` evidence illustrates this distinction. Packagist identifies `https://github.com/boctulus/simplerest.git` and package commit `c15d0627afeae9d6bdd0067ee917b8ec7c043d41` for both source and dist. That standalone root commit records that it was assembled from canonical source commit `b7a48ac6d1c2c7bf99794b3e7d5ad6a70742c238`; it is not the canonical `master` commit itself. A future PACK result is only a candidate until the separately published immutable tag and Packagist metadata resolve to its verified snapshot commit.

## Composer core boundary

The curated core contains only:

- `composer.json`;
- `LICENSE`;
- `README.md`;
- the complete committed `src/framework/` tree selected from the source commit.

The root development checkout may define Composer hooks for repository-only workflows. PACK deterministically removes root-only `scripts` entries from the curated Composer manifest because their implementation files are outside the package boundary.

The core must not contain PACK implementation files, `com`, application commands, internal tooling, tests, documentation, archives, `vendor/`, unrelated application code, or optional package trees. In particular, `scripts/pack/`, `scripts/pack_framework.php`, and `app/Commands/pack/` never enter the Composer package.

This contract does not authorize extracting, rewriting, or removing additional framework libraries.

## Core PACK and legacy starter generation

`php com pack build`, its compatibility aliases `pack run` and `pack package`, and `php com pack dry-run` all use `scripts/pack/build-core.php`. The core builder runs before the application bootstrap and is the sole Composer curation implementation.

`scripts/pack_framework.php` is a separate legacy starter-application generator retained for the distribution verification suite. Direct use requires the explicit form:

```powershell
php scripts/pack_framework.php --legacy-starter <source> <new-disposable-destination>
```

The starter generator accepts only a destination that does not exist. It does not refresh `simplerest-pack`, and it is not a Composer core source.

## Source commit selection

Without `--source-commit`, PACK resolves and reports canonical `HEAD`. Before building it rejects staged, unstaged, untracked, or ignored changes that affect:

- `composer.json`;
- `LICENSE`;
- `README.md`;
- `src/framework/`.

Those are the only working-tree paths whose bytes or manifest contract can affect the curated artifact. Unrelated application changes, tests, documentation, and PHPUnit cache files do not block a default build.

`--source-commit=<sha>` deliberately selects an immutable commit. PACK reports the selected commit, current HEAD, and whether the selection is the current HEAD or a historical commit. It never chooses an older commit as a fallback. A historical PACK is valid for reproduction and investigation, but the release snapshot gate requires the approved source commit to be checked out as HEAD before creating a release candidate.

## Destination preflight and ownership

A missing destination is created only after every source file has been staged and validated. An existing destination is recognized only when all of these conditions hold:

- it is a real directory outside the source tree, not a symlink or junction;
- it is the root of its own Git worktree;
- the worktree has no staged, unstaged, untracked, or ignored files;
- every tracked file belongs to the approved core boundary;
- the filesystem exactly matches the tracked files;
- its Composer package name is `boctulus/simplerest`.

This permits a clean curated Git worktree to be refreshed while preserving `.git`. A full application tree, markerless non-Git directory, or older incompatible layout is not migrated automatically. The operator must preserve its work and perform any one-time migration under separate review.

The current `D:\laragon\www\simplerest-pack` worktree may contain substantial uncommitted work. PACK must reject it without resetting, cleaning, stashing, deleting, or overwriting anything.

## Staging and replacement

PACK reads committed blobs, validates PHP syntax, and constructs the complete candidate in a uniquely named sibling staging directory. A new destination is installed with a same-parent rename.

For a clean recognized Git destination, PACK moves only the previously tracked package files into an owned sibling rollback directory, installs only the staged managed files, and leaves `.git` in place. If replacement fails, it removes only files installed by that attempt and restores the previous managed files. Owned staging and rollback directories are removed only after their parent and generated name have been verified.

PACK never performs an unconditional recursive cleanup and never deletes an unexpected destination file.

## Preflight, dry-run, help, and errors

These commands share the same source and destination preflight:

```powershell
php com pack build --profile=core --dry-run
php com pack dry-run
```

A dry-run reports the exact source commit and selection mode, validates paths and Git state, and prints `CREATE`, `KEEP`, `UPDATE`, and managed `REMOVE` operations. It creates no directory and modifies no file.

`php com pack build --help` and `php com pack dry-run --help` display help without starting preflight or a build. Unsupported options, invalid paths, dirty state, unsafe links, incompatible layouts, and validation failures return a nonzero exit code. There is no `--force` bypass.

## Release provenance and publication

PACK prints its selected source SHA but stores no implementation metadata inside the Composer microcore. VERIFY creates external provenance.

`build-core-source-snapshot.ps1` requires a fresh output directory and:

1. requires `SourceCommit` to equal the checked-out approved canonical HEAD;
2. verifies every curated framework and package-level file against that commit, including the documented Composer manifest transformation;
3. rejects extra files and PACK-tool leakage;
4. checks that the intended release version belongs to the committed branch-alias series;
5. creates an isolated package tree and package commit;
6. writes external `release-provenance.json` containing the canonical repository, canonical source SHA, file hashes, archive hash, package tree, package commit, and intended version.

`scripts/composer/verify-installed-core.php` compares a Composer-installed candidate byte-for-byte with that external record and independently rejects PACK implementation paths.

The snapshot and verification operations create no remote reference. Publication requires a separate explicit decision, a new immutable public tag, and confirmation that Packagist resolves the reviewed package commit. Existing public tags must never be rewritten or force-pushed.

## Recovery from interruption

If PACK stops before replacement, the destination is unchanged and only an owned sibling stage may remain. If it stops during a managed refresh, rerun preflight before doing anything else and inspect sibling directories named `.simplerest-core-stage-*` or `.simplerest-core-backup-*`. Never delete such a directory merely by pattern: verify its resolved parent, generated name, contents, and relationship to the interrupted destination first.

If PACK reports that rollback needs manual recovery, preserve both the destination and the reported rollback directory. Do not rerun PACK, clean Git, or move files until the tracked destination and rollback contents have been compared.

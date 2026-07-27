---
name: git-worktree-policies
description: Define deterministic naming, validation, safety rules, and reporting requirements for Git linked worktrees.
---

# SKILL_DEFINITION: Git Worktree policies

## Scope

This skill defines policies for creating, locating, validating, and operating
inside Git linked worktrees.

It does not authorize merges, rebases, branch deletion, force pushes, or
destructive repository operations.

## Worktree directory naming

Every newly created linked worktree must use a deterministic directory name derived from:

1. The directory name of the primary worktree.
2. The complete branch name assigned to the linked worktree.

The directory name must follow this format:

```text
<repository-directory>-wt-<branch-slug>
```

The branch slug must be generated using the complete branch name:

* convert it to lowercase;
* replace `/` with `-`;
* replace any remaining unsupported character with `-`;
* collapse consecutive hyphens;
* remove leading or trailing hyphens.

Example:

```text
Primary worktree directory:
goprop

Branch:
docs/trial-organizations-and-provisioning

Linked worktree directory:
goprop-wt-docs-trial-organizations-and-provisioning
```

The full branch name must be used. Do not select only some branch keywords.

This naming rule applies when creating new worktrees. An existing worktree must not be considered invalid solely because its directory predates this convention.

## How to validate a worktree

Directory naming is only a convention and must not be used as proof that a directory is a Git worktree.

After moving to the directory specified by the user or task, verify all of the following:

1. The directory belongs to a Git repository.
2. `git rev-parse --show-toplevel` resolves to the requested directory itself, after normalizing both paths.
3. The resolved path appears as a worktree in:

```bash
git worktree list --porcelain
```

4. The resolved path is not the primary worktree shown by `git worktree list --porcelain`.
5. The worktree has an attached local branch and is not in detached `HEAD` state.
6. The current branch is not the repository's default branch.
7. A remote named `origin` exists.

Determine the primary worktree and linked worktrees from Git metadata. Do not infer them from:

* the directory name alone;
* occurrences of that name in documentation;
* `README.md`;
* `CLAUDE.md`;
* changelog files;
* assumptions about neighboring directories.

If any validation fails, stop without pushing and report the exact failed condition.

# Must not share dependency installation directories

**Important:**

Do not share dependency installation directories such as `vendor/` or `node_modules/` between worktrees. Each worktree must maintain its own dependency installation.

Package-manager caches may be shared, provided they remain external to the worktree and are managed by the package manager itself.

## How to push a worktree branch

Move to the directory explicitly specified for the operation and validate it using the rules above.

Do not change branches.

Determine the current branch from Git and verify that it is not the default branch, normally `main` or `master`. Prefer the default branch identified by `origin/HEAD` instead of assuming its name.

Before pushing, verify:

* the worktree is clean;
* `HEAD` belongs to the current branch;
* the current branch has not changed during validation;
* the configured upstream, if present, is `origin/<current-branch>`.

If the worktree contains tracked or untracked changes, stop and report them. Do not commit, discard, stash or modify those changes unless explicitly requested.

If no upstream exists, push with:

```bash
git push --set-upstream origin <current-branch>
```

If the upstream is already `origin/<current-branch>`, push normally:

```bash
git push
```

If the existing upstream points to a different remote or branch, stop and report the mismatch. Do not silently replace it.

Do not:

* change branches;
* create commits;
* amend commits;
* merge;
* rebase;
* reset;
* stash;
* discard changes;
* modify repository files;
* delete branches;
* push any branch other than the active branch;
* force-push.

A fetch used exclusively to verify the final remote state is allowed because it does not modify the worktree files.

## Final verification

After the push:

1. Refresh the remote-tracking reference for the pushed branch.
2. Confirm that the active local branch has not changed.
3. Confirm that the worktree remains clean.
4. Calculate the ahead/behind relationship between `HEAD` and `origin/<current-branch>`.

The expected successful final relationship is:

```text
ahead: 0
behind: 0
```

## Required report

At completion, report:

* requested directory;
* resolved worktree root;
* primary worktree directory;
* current local branch;
* repository default branch;
* `HEAD` commit hash and subject;
* worktree status;
* configured upstream;
* result of the push;
* ahead/behind relationship against `origin/<current-branch>`;
* confirmation that no branch change, merge, rebase or additional repository modification was performed.

If the operation is blocked, report the failed validation and do not continue.

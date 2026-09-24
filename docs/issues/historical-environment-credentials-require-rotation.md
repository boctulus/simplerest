# Historical environment credentials require rotation review

**Status:** open; credential validity and provider-side rotation remain unverified

**Confidence:** confirmed non-empty literals in secret-named fields across historical snapshots and current branch tips; provider validity is not established by Git

**Severity:** high; release gate

## Finding

Yes, credential-like values remain in branch history and in several branch tips. The review was limited to `.env`, `.env.example`, and `config/config.php` paths. It found provider-token-format strings and a private-key block, along with non-empty literals under database, mail, Redis, JWT, session, refresh, application, and client secret fields. Treat every unverified candidate as exposed. No credential values are reproduced in this report or its ledgers.

The Google Maps API key field in `config/config.php` contains a value matching a Google API key format at the tips of all four live remote branches. Git does not show whether the provider restricts those keys or whether they remain active. A Firebase private-key block also remains in a local branch tip. The Firebase web API key is a public identifier in some Firebase configurations, so its appearance alone does not establish a secret; its provider restrictions were not checked.

Non-secret-looking values were separated from candidates: OAuth callback URLs and client IDs, Firebase auth domains, blank values, and obvious placeholders were not counted as secret findings. Values in `.env.example` and short/default-looking passwords remain candidates until an owner confirms their purpose and scope.

## Audit coverage — 2026-09-24

I enumerated and reviewed the scoped file snapshots in every commit reachable from 38 branch refs: two local branches and 36 `origin` tracking refs. A live `git ls-remote --heads origin` check found four current remote branches; the other 32 `origin` tracking refs are stale local refs for branches no longer advertised by `origin`. The symbolic `origin/HEAD` alias was excluded.

The review covered 5,059 unique commits, 15,184 snapshots of the scoped files, and 245 unique path/content versions across these six paths:

- `.env`
- `.env.example`
- `config/config.php`
- `docs/dev/livewire (Laravel)/livewirekit-dropdowns/demo/.env.example`
- `docs/extras/livewire (Laravel)/livewirekit-dropdowns/demo/.env.example`
- `docs/livewire (Laravel)/livewirekit-dropdowns/demo/.env.example`

The commit ledger has 13,749 commit/file rows with non-empty values under secret-named fields after excluding empty values and obvious placeholders. A row lists every branch ref that reaches that commit. The branch IDs in the ledger map to full ref names, tip commits, and branch states in the branch index. This records file, branch, and commit without recording credential values.

### Branch tips at audit cutoff

The branch tips below are the frozen refs used for this review, before the audit publication commit. That commit contains only this report, its ledgers, and the task record; it does not change any scoped file.

| Branch ref | Tip commit | Candidate in scoped files |
| --- | --- | --- |
| `origin/agent/http-query-stage-1` | `d20bdf3c3d9e8f60e4727efcb9dfd67d450e789b` | `config/config.php`: `google_maps_api_key` |
| `origin/docs/rebuild-documentation-audit` | `60ff474a674ffc9638be0e3d9486ff87c6fa129e` | `.env.example`: `DB_PASSWORD`; `config/config.php`: `google_maps_api_key` |
| `origin/main` | `27939f7331dda7595eb3026684db6ab74b9c1e18` | `config/config.php`: `google_maps_api_key` |
| `origin/master` | `38b1e50014f97e47ddc670bed29c66e2fbb69852` | `.env.example`: `DB_PASSWORD`; `config/config.php`: `google_maps_api_key` |

The local branch `refs/heads/docs/rebuild-documentation-audit` is at `3ca9dbd7c510c2bddfdc5984b6eaa6d33cb44b7f`; its `.env` contains provider-token-format strings, a Firebase private-key block, and non-empty database and mail password fields. This local tip differs from the live `origin/docs/rebuild-documentation-audit` tip. Stale tracking refs also retain these paths; `refs/remotes/origin/dev` at `58def981f1415e04f701b00f33b9ad5eac5797a8` has non-empty `Password`, `app_secret`, and `client_secret` fields in `config/config.php`.

### Evidence files

- [Branch index](../security/audits/historical-environment-credentials-branch-index.csv) — all 38 refs, ref state, tip commit, and reachable commit count.
- [Branch-tip findings](../security/audits/historical-environment-credentials-branch-tips.csv) — scoped files and candidate field names at each ref tip.
- [Commit ledger](../security/audits/historical-environment-credentials-commit-ledger.csv) — every candidate-bearing commit/file snapshot, branch IDs, content blob ID, field names, and redacted classification.

`provider-token-format` means the literal matched a recognizable token/key format; it does not prove the provider still accepts it. Other non-empty literals were classified by sensitive field name and remain unverified. The branch-ref ledger excludes `refs/original/*` and other non-branch refs; their exposure status was not assessed here.

## Required before release

1. Treat every unverified value in the ledger as exposed. Revoke or rotate confirmed API keys, private keys, passwords, and signing secrets, including database, mail, Redis, JWT, session, refresh, application, and client credentials.
2. Verify the Google Maps key restrictions and rotate it if its intended API and source restrictions cannot be confirmed. Rotate the Firebase private key; review Firebase API restrictions for the public web key.
3. Remove credential-bearing files from active and retained branch refs where appropriate. History cleanup does not revoke credentials.
4. Record provider-side evidence for each rotation or explicit confirmation that a candidate was a placeholder or non-secret identifier.

No provider-side validation or credential rotation was performed as part of this audit. Rewriting Git history is a separate operation and does not replace revocation.

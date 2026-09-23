# Historical environment credentials require rotation review

**Status:** open

**Confidence:** confirmed non-empty credential-like values in tracked history reachable from active remote branches; provider validity and rotation are unverified

**Severity:** high; release gate

## Finding

The `.env` path appears in 42 commits reachable from `origin/master`: 41 commits store a `.env` snapshot and one removes the file. Each stored snapshot has at least one non-empty value under a field whose name indicates a password, secret, token, key, credential, or auth value. The fields include database and mail passwords, OAuth client secrets, Redis credentials, and third-party API key variables for Google, OpenAI, Claude, DeepSeek, Gemini, SendGrid, Sendinblue, and Typesense. Some values may be placeholders; this repository cannot establish which values were live or whether they were revoked. No values are reproduced here.

The Google Maps key previously hardcoded in `config/config.php` also appears in the fetched `origin/master` history. The current configuration now reads `GOOGLE_MAPS_API_KEY`, but removing a value from the current tree does not revoke it or remove it from Git history.

The tracked `etc/zippycart_credentials.json` contains a Firebase web configuration key. Firebase documents those client keys as public identifiers when restricted to Firebase services; the live API allowlist and Firebase security rules were not inspected.

## Branch verification — 2026-09-24

The current `origin` heads were refreshed and checked. The root `.env` history is reachable from two active remote branches:

| Remote branch | Head | `.env` path history | `.env` at tip |
| --- | --- | --- | --- |
| `master` | `763f147` | 42 path commits; 41 stored snapshots with non-empty credential-like values | No |
| `docs/rebuild-documentation-audit` | `60ff474` | 42 path commits; 41 stored snapshots with non-empty credential-like values | No |
| `main` | `27939f7` | No root `.env` history found | No |
| `agent/http-query-stage-1` | `d20bdf3` | No root `.env` history found | No |

The local backup refs `refs/original/refs/heads/master` and `refs/original/refs/remotes/origin/master` also still point to commits containing `.env`. Removing the file from the active branch tips did not remove its historical snapshots. This branch check covers the root `.env` path; it does not establish that other paths are free of secrets. Provider-side rotation evidence remains unavailable, so this finding stays open.

## Required before release

1. Review the historical `.env` fields privately and identify which non-empty values were real credentials. Do not paste the values into issues, logs, or chat.
2. Revoke or rotate every confirmed real credential that was exposed, including credentials belonging to external providers and shared infrastructure.
3. Replace the Maps key with a distinct server-side key restricted to the Geocoding API and approved server IPs, after checking usage and dependencies.
4. Verify Firebase keys are limited to Firebase APIs and that Firebase Security Rules/App Check protect the relevant resources.
5. Confirm completion with provider-side evidence. Treat any value whose status is unknown as exposed.

History rewriting is a separate repository operation; it does not replace provider-side revocation and is not part of this finding's remediation.

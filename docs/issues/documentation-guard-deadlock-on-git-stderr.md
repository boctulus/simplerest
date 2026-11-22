# Documentation guard hangs when Git writes a lot to stderr

**Status:** RESUELTO (2026-11-22, with maintainer approval in chat)

**Confidence:** reproduced on 2026-11-22 on Windows 11, PHP 8.3.36, with about 80 renamed files in the working tree

## Problem

`scripts/documentation-governance-guard.php` runs Git through `runCommand()`, which reads stdout to EOF before it reads stderr. When Git writes more to stderr than the pipe buffer holds — for example one `LF will be replaced by CRLF` warning per changed file under `core.autocrlf` — Git blocks on stderr while the guard blocks on stdout. The guard never finishes.

## Workaround

Suppress the CRLF warnings for that run only:

```powershell
$env:GIT_CONFIG_PARAMETERS = "'core.safecrlf=false'"; php scripts/documentation-governance-guard.php; Remove-Item Env:GIT_CONFIG_PARAMETERS
```

```bash
GIT_CONFIG_PARAMETERS="'core.safecrlf=false'" php scripts/documentation-governance-guard.php
```

## Proposed fix

In `runCommand()`, send stderr to a temporary file (`2 => ['file', $tmp, 'w']`) and read it after `proc_close()`. `stream_select()` on `proc_open()` pipes is not reliable on Windows, so a select loop is not a fix there. Applied on 2026-11-22. Because the guard blocks changes to its own file, the run that contains this change needs `--approved-breaking-change="maintainer chat 2026-11-22: fix guard deadlock on Git stderr"`. Afterwards the guard completed with exit code 0 without the workaround.

<#
.SYNOPSIS
    Shows files changed in the last N commits.

.DESCRIPTION
    Translation + enhancement of git_diff_with_hash.sh.
    Displays commit hash range and changed file names.

.PARAMETER Commits
    Number of commits to look back. Default: 1.

.PARAMETER Stat
    Show git diff --stat instead of just file names.

.EXAMPLE
    .\git-changed-files.ps1
    .\git-changed-files.ps1 3
    .\git-changed-files.ps1 5 -Stat
#>
param(
    [int]$Commits = 1,
    [switch]$Stat
)

if ($Commits -lt 1) {
    Write-Host "Error: Commits must be >= 1" -ForegroundColor Red
    exit 1
}

# Ensure we're in a git repo
try {
    $null = git rev-parse --is-inside-work-tree 2>&1
    if ($LASTEXITCODE -ne 0) { throw "Not a git repo" }
} catch {
    Write-Host "Error: Not inside a Git repository." -ForegroundColor Red
    exit 1
}

$headHash = (git rev-parse HEAD).Substring(0, 10)
$targetHash = (git rev-parse HEAD~$Commits).Substring(0, 10)

Write-Host ""
Write-Host "Commits: HEAD ($headHash) ~ HEAD~$Commits ($targetHash)" -ForegroundColor Cyan
Write-Host ("-" * 60) -ForegroundColor DarkGray

if ($Stat) {
    git diff --stat HEAD~$Commits..HEAD
} else {
    git diff --name-only HEAD~$Commits..HEAD
}

Write-Host ("-" * 60) -ForegroundColor DarkGray
$changedCount = (git diff --name-only HEAD~$Commits..HEAD | Measure-Object -Line).Lines
Write-Host "Total: $changedCount file(s)" -ForegroundColor Yellow

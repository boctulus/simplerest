[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [string]$CuratedDirectory,

    [string]$SourceCommit = 'HEAD',

    [Parameter(Mandatory = $true)]
    [string]$OutputDirectory,

    [ValidatePattern('^v?\d+\.\d+\.\d+(-[0-9A-Za-z.-]+)?$')]
    [string]$Version = '3.0.0'
)

$ErrorActionPreference = 'Stop'

$repositoryRoot = (git rev-parse --show-toplevel).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($repositoryRoot)) {
    throw 'Run this script from the SimpleRest Git working tree.'
}

$resolvedCommit = (git rev-parse "$SourceCommit^{commit}").Trim()
if ($LASTEXITCODE -ne 0 -or $resolvedCommit -notmatch '^[0-9a-f]{40}$') {
    throw "SourceCommit '$SourceCommit' does not resolve to an immutable Git commit."
}

$manifestJson = git show "${resolvedCommit}:composer.json" | ConvertFrom-Json
if ($LASTEXITCODE -ne 0 -or $manifestJson.name -ne 'boctulus/simplerest') {
    throw "Commit $resolvedCommit does not contain the existing boctulus/simplerest package manifest."
}

$outputPath = [System.IO.Path]::GetFullPath($OutputDirectory)
$legacyPackPath = [System.IO.Path]::GetFullPath((Join-Path (Split-Path -Parent $repositoryRoot) 'simplerest-pack'))
if ($outputPath.TrimEnd('\') -ieq $legacyPackPath.TrimEnd('\')) {
    throw 'The legacy simplerest-pack directory cannot be used for Composer source snapshots.'
}
if (Test-Path -LiteralPath $outputPath) {
    throw "OutputDirectory already exists; refusing to overwrite or clean it: $outputPath"
}

$parentPath = Split-Path -Parent $outputPath
if (-not (Test-Path -LiteralPath $parentPath -PathType Container)) {
    throw "OutputDirectory parent must already exist and be verified: $parentPath"
}

$archivePath = Join-Path $outputPath 'core-source.zip'
$packagePath = Join-Path $outputPath 'repository'

# Composer MUST consume the curated result of "php com pack build".
# Never create a Composer package by archiving the development tree.
if (-not (Test-Path -LiteralPath $CuratedDirectory -PathType Container)) {
    throw "Missing curated pack. First run php com pack build --profile=core: $CuratedDirectory"
}
$curatedPath = (Resolve-Path -LiteralPath $CuratedDirectory).Path.TrimEnd('\', '/')
$sourcePath = [System.IO.Path]::GetFullPath($repositoryRoot).TrimEnd('\', '/')
if ($curatedPath -ieq $sourcePath -or $curatedPath -ieq $outputPath) {
    throw 'Curated input, canonical source, and isolated snapshot output must be distinct directories.'
}

# The pack-first, versioned allowlist defines the published microcore.
# This must NOT enumerate every file in the development src/framework tree.
$paths = @(git show ($resolvedCommit + ':scripts/pack/core-files.txt'))
if ($LASTEXITCODE -ne 0) {
    throw 'Could not load the committed microcore curation manifest.'
}
$paths = @($paths | Where-Object { $_ -ne '' })
if ($paths.Count -ne 17 -or @($paths | Select-Object -Unique).Count -ne $paths.Count) {
    throw 'The curated core must have 17 unique files (3 root files plus 14 microcore PHP classes).'
}
foreach ($required in @('composer.json', 'LICENSE', 'README.md')) {
    if ($paths -notcontains $required) {
        throw "Required microcore source file missing: $required"
    }
}
$allow = @{}
foreach ($path in $paths) {
    if ($path -notin @('composer.json', 'LICENSE', 'README.md') -and
        $path -cnotmatch '^src/framework/[A-Za-z0-9_/-]+\.php$') {
        throw "Unexpected file in microcore source: $path"
    }
    $allow[$path] = $true
    $inputFile = Join-Path $curatedPath ($path.Replace('/', [System.IO.Path]::DirectorySeparatorChar))
    if (-not (Test-Path -LiteralPath $inputFile -PathType Leaf)) {
        throw "Pack output is missing $path. Rerun php com pack build."
    }
    $sourceBlob = (git rev-parse ($resolvedCommit + ':' + $path)).Trim()
    if ($LASTEXITCODE -ne 0) {
        throw "Cannot resolve source blob: $path"
    }
    $packBlob = (git hash-object -- $inputFile).Trim()
    if ($LASTEXITCODE -ne 0 -or $packBlob -cne $sourceBlob) {
        throw "Pack output differs from source commit at $path. Rebuild the curated pack using --source-commit=$resolvedCommit."
    }
}
foreach ($candidate in @(Get-ChildItem -LiteralPath $curatedPath -File -Force -Recurse)) {
    $relative = $candidate.FullName.Substring($curatedPath.Length + 1).Replace('\', '/')
    if ($relative -eq '.git' -or $relative.StartsWith('.git/')) { continue }
    if (-not $allow.ContainsKey($relative)) {
        throw "Uncurated file in pack worktree: $relative. Refusing to publish development/legacy content."
    }
}
$manifest = Get-Content -LiteralPath (Join-Path $curatedPath 'composer.json') -Raw | ConvertFrom-Json
if ($manifest.name -ne 'boctulus/simplerest' -or
    (@($manifest.require.PSObject.Properties.Name) -join ',') -cne 'php' -or
    $manifest.PSObject.Properties.Name -contains 'require-dev' -or
    $manifest.PSObject.Properties.Name -contains 'suggest' -or
    $manifest.autoload.PSObject.Properties.Name -contains 'files') {
    throw 'Curated Composer manifest is not the dependency-free SimpleRest microcore.'
}

$null = New-Item -ItemType Directory -Path $outputPath
$null = New-Item -ItemType Directory -Path $packagePath

# Copy only the pack output. This is the ONLY source of release bytes.
foreach ($path in $paths) {
    $inputFile = Join-Path $curatedPath ($path.Replace('/', [System.IO.Path]::DirectorySeparatorChar))
    $releaseFile = Join-Path $packagePath ($path.Replace('/', [System.IO.Path]::DirectorySeparatorChar))
    $dir = Split-Path -Parent $releaseFile
    if (-not (Test-Path -LiteralPath $dir)) {
        $null = New-Item -ItemType Directory -Path $dir -Force
    }
    Copy-Item -LiteralPath $inputFile -Destination $releaseFile
}
Compress-Archive -Path (Join-Path $packagePath '*') -DestinationPath $archivePath

$sourceDate = (git show -s --format=%cI $resolvedCommit).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($sourceDate)) {
    throw "Could not read the source commit date for $resolvedCommit."
}

git -C $packagePath init
if ($LASTEXITCODE -ne 0) { throw 'Could not initialize the isolated core source repository.' }
git -C $packagePath add --all
if ($LASTEXITCODE -ne 0) { throw 'Could not stage the isolated core source snapshot.' }

$tree = (git -C $packagePath write-tree).Trim()
if ($LASTEXITCODE -ne 0 -or $tree -notmatch '^[0-9a-f]{40,64}$') {
    throw 'Could not write the isolated core source tree.'
}

$commitMessage = "Composer package boctulus/simplerest from $resolvedCommit"
$oldAuthorName = $env:GIT_AUTHOR_NAME
$oldAuthorEmail = $env:GIT_AUTHOR_EMAIL
$oldCommitterName = $env:GIT_COMMITTER_NAME
$oldCommitterEmail = $env:GIT_COMMITTER_EMAIL
$oldAuthorDate = $env:GIT_AUTHOR_DATE
$oldCommitterDate = $env:GIT_COMMITTER_DATE
try {
    $env:GIT_AUTHOR_NAME = 'SimpleRest Composer Release'
    $env:GIT_AUTHOR_EMAIL = 'composer-release@boctulus.local'
    $env:GIT_COMMITTER_NAME = 'SimpleRest Composer Release'
    $env:GIT_COMMITTER_EMAIL = 'composer-release@boctulus.local'
    $env:GIT_AUTHOR_DATE = $sourceDate
    $env:GIT_COMMITTER_DATE = $sourceDate
    $snapshotCommit = (git -C $packagePath commit-tree $tree -m $commitMessage).Trim()
    if ($LASTEXITCODE -ne 0 -or $snapshotCommit -notmatch '^[0-9a-f]{40,64}$') {
        throw 'Could not create the isolated core source commit.'
    }
}
finally {
    $env:GIT_AUTHOR_NAME = $oldAuthorName
    $env:GIT_AUTHOR_EMAIL = $oldAuthorEmail
    $env:GIT_COMMITTER_NAME = $oldCommitterName
    $env:GIT_COMMITTER_EMAIL = $oldCommitterEmail
    $env:GIT_AUTHOR_DATE = $oldAuthorDate
    $env:GIT_COMMITTER_DATE = $oldCommitterDate
}

git -C $packagePath update-ref refs/heads/main $snapshotCommit
if ($LASTEXITCODE -ne 0) { throw 'Could not set the package repository main ref.' }
git -C $packagePath symbolic-ref HEAD refs/heads/main
if ($LASTEXITCODE -ne 0) { throw 'Could not set the package repository HEAD.' }
git -C $packagePath tag $Version $snapshotCommit
if ($LASTEXITCODE -ne 0) { throw "Could not create local validation tag '$Version'." }

Write-Output "Curated pack:  $curatedPath"
Write-Output "Package:       boctulus/simplerest (existing identity)"
Write-Output "Source commit: $resolvedCommit"
Write-Output "Package tree:  $tree"
Write-Output "Package commit: $snapshotCommit"
Write-Output "Local tag:     $Version"
Write-Output "Source repo:   $packagePath"
Write-Output 'No remote or published tag was changed.'

[CmdletBinding()]
param(
    [string]$SourceCommit = 'HEAD',

    [Parameter(Mandatory = $true)]
    [string]$OutputDirectory,

    [ValidatePattern('^v?\d+\.\d+\.\d+(-[0-9A-Za-z.-]+)?$')]
    [string]$Version = '2.0.0'
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

$null = New-Item -ItemType Directory -Path $outputPath
$archivePath = Join-Path $outputPath 'core-source.zip'
$packagePath = Join-Path $outputPath 'repository'
$null = New-Item -ItemType Directory -Path $packagePath

git archive --format=zip --output=$archivePath $resolvedCommit -- composer.json LICENSE README.md src/framework
if ($LASTEXITCODE -ne 0) {
    throw "Could not create the core source snapshot from $resolvedCommit."
}

Expand-Archive -LiteralPath $archivePath -DestinationPath $packagePath
$expectedTopLevel = @('LICENSE', 'README.md', 'composer.json', 'src')
$actualTopLevel = @(Get-ChildItem -LiteralPath $packagePath -Force | Select-Object -ExpandProperty Name | Sort-Object)
if (($actualTopLevel -join "`n") -cne (($expectedTopLevel | Sort-Object) -join "`n")) {
    throw "Unexpected files in core snapshot. Expected only: $($expectedTopLevel -join ', '); found: $($actualTopLevel -join ', ')."
}

$assetPattern = '(?i)\.(js|mjs|cjs|ts|tsx|jsx|css|scss|sass|less|map|woff2?|ttf|otf|eot|svg|png|jpe?g|gif|webp|ico)$'
$assets = @(Get-ChildItem -LiteralPath $packagePath -File -Recurse | Where-Object { $_.Name -match $assetPattern })
if ($assets.Count -gt 0) {
    $assetList = $assets | ForEach-Object { $_.FullName.Substring($packagePath.Length + 1) }
    throw "The core snapshot must not contain frontend or third-party assets:`n$($assetList -join [Environment]::NewLine)"
}

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

Write-Output "Package:       boctulus/simplerest (existing identity)"
Write-Output "Source commit: $resolvedCommit"
Write-Output "Package tree:  $tree"
Write-Output "Package commit: $snapshotCommit"
Write-Output "Local tag:     $Version"
Write-Output "Source repo:   $packagePath"
Write-Output 'No remote or published tag was changed.'

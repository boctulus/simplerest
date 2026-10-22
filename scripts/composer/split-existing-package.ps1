[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('boctulus/simplerest-pdf', 'boctulus/simplerest-code-tools')]
    [string]$Package,

    [string]$SourceCommit = 'HEAD',

    [ValidatePattern('^v?\d+\.\d+\.\d+(-[0-9A-Za-z.-]+)?$')]
    [string]$Version,

    [switch]$Push
)

$ErrorActionPreference = 'Stop'

$packageDefinitions = @{
    'boctulus/simplerest-pdf' = @{
        Path = 'packages/boctulus/simplerest-pdf'
        Repository = 'boctulus/simplerest-pdf'
        Remote = 'git@github.com:boctulus/simplerest-pdf.git'
    }
    'boctulus/simplerest-code-tools' = @{
        Path = 'packages/boctulus/simplerest-code-tools'
        Repository = 'boctulus/simplerest-code-tools'
        Remote = 'git@github.com:boctulus/simplerest-code-tools.git'
    }
}

$definition = $packageDefinitions[$Package]
$repositoryRoot = (git rev-parse --show-toplevel).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($repositoryRoot)) {
    throw 'Run this script from a SimpleRest Git working tree.'
}

$resolvedCommit = (git rev-parse "$SourceCommit^{commit}").Trim()
if ($LASTEXITCODE -ne 0 -or $resolvedCommit -notmatch '^[0-9a-f]{40}$') {
    throw "SourceCommit '$SourceCommit' does not resolve to an immutable Git commit."
}

$manifestJson = git show "${resolvedCommit}:$($definition.Path)/composer.json" | ConvertFrom-Json
if ($LASTEXITCODE -ne 0 -or $manifestJson.name -ne $Package) {
    throw "Commit $resolvedCommit does not contain the expected package manifest for '$Package'."
}

$packageFiles = @(git ls-tree -r --name-only $resolvedCommit -- $definition.Path)
if ($LASTEXITCODE -ne 0) {
    throw "Could not inspect the file list for '$Package' at $resolvedCommit."
}

$frontendAssetPattern = '(?i)\.(js|mjs|cjs|ts|tsx|jsx|css|scss|sass|less|map|woff2?|ttf|otf|eot|svg|png|jpe?g|gif|webp|ico)$'
$frontendAssets = @($packageFiles | Where-Object { $_ -match $frontendAssetPattern })
if ($frontendAssets.Count -gt 0) {
    $assetList = $frontendAssets -join [Environment]::NewLine
    throw "Composer package '$Package' must not contain frontend or third-party assets. Remove these files from the package source before splitting:`n$assetList"
}

$splitOutput = & git subtree split -q "--prefix=$($definition.Path)" $resolvedCommit
if ($LASTEXITCODE -ne 0) {
    throw "git subtree split failed for '$Package' at $resolvedCommit."
}

$splitCommit = @($splitOutput | Where-Object { $_ -match '^[0-9a-f]{40}$' } | Select-Object -Last 1)
if ($splitCommit.Count -ne 1) {
    throw "git subtree split did not return a package commit for '$Package'."
}

Write-Output "Package:       $Package"
Write-Output "Source commit: $resolvedCommit"
Write-Output "Package tree:  $splitCommit"
Write-Output "Target repo:   $($definition.Repository)"

if (-not $Push) {
    Write-Output 'Dry run only. Re-run with -Push after package tests and release review pass.'
    return
}

$null = gh repo view $definition.Repository --json nameWithOwner
if ($LASTEXITCODE -ne 0) {
    throw "Target repository '$($definition.Repository)' must exist before publishing."
}

if ($Version) {
    $existingTag = git ls-remote --refs $definition.Remote "refs/tags/$Version"
    if ($LASTEXITCODE -ne 0) {
        throw "Could not inspect tags in '$($definition.Repository)'."
    }
    if (-not [string]::IsNullOrWhiteSpace(($existingTag -join ''))) {
        throw "Tag '$Version' already exists in '$($definition.Repository)'; release tags are immutable."
    }
}

git push $definition.Remote "$($splitCommit[0]):refs/heads/main"
if ($LASTEXITCODE -ne 0) {
    throw "Could not push the package split to '$($definition.Repository)' main. No force push was attempted."
}

if ($Version) {
    git push $definition.Remote "$($splitCommit[0]):refs/tags/$Version"
    if ($LASTEXITCODE -ne 0) {
        throw "Could not create new tag '$Version' in '$($definition.Repository)'."
    }
}

Write-Output 'Package split published without force-updating any ref.'

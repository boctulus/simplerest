[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet(
        'boctulus/simplerest-fpdf',
        'boctulus/simplerest-scaffolding',
        'boctulus/simplerest-bootstrap-html',
        'boctulus/simplerest-web-inspection',
        'boctulus/simplerest-api-clients',
        'boctulus/llm-providers'
    )]
    [string]$Package,

    [string]$SourceCommit = 'HEAD',

    [ValidatePattern('^v?\d+\.\d+\.\d+(-[0-9A-Za-z.-]+)?$')]
    [string]$Version,

    [ValidateSet('FilterRepo', 'Subtree')]
    [string]$Strategy = 'FilterRepo',

    [switch]$PersistSplit,

    [switch]$Push
)

$ErrorActionPreference = 'Stop'

$packageDefinitions = @{
    'boctulus/simplerest-fpdf' = @{
        Path = 'packages/boctulus/simplerest-fpdf'
        Repository = 'boctulus/simplerest-fpdf'
        Remote = 'git@github.com:boctulus/simplerest-fpdf.git'
    }
    'boctulus/simplerest-scaffolding' = @{
        Path = 'packages/boctulus/simplerest-scaffolding'
        Repository = 'boctulus/simplerest-scaffolding'
        Remote = 'git@github.com:boctulus/simplerest-scaffolding.git'
    }
    'boctulus/simplerest-bootstrap-html' = @{
        Path = 'packages/boctulus/simplerest-bootstrap-html'
        Repository = 'boctulus/simplerest-bootstrap-html'
        Remote = 'git@github.com:boctulus/simplerest-bootstrap-html.git'
    }
    'boctulus/simplerest-web-inspection' = @{
        Path = 'packages/boctulus/simplerest-web-inspection'
        Repository = 'boctulus/simplerest-web-inspection'
        Remote = 'git@github.com:boctulus/simplerest-web-inspection.git'
    }
    'boctulus/simplerest-api-clients' = @{
        Path = 'packages/boctulus/simplerest-api-clients'
        Repository = 'boctulus/simplerest-api-clients'
        Remote = 'git@github.com:boctulus/simplerest-api-clients.git'
    }
    'boctulus/llm-providers' = @{
        Path = 'packages/boctulus/llm-providers'
        Repository = 'boctulus/llm-providers'
        Remote = 'git@github.com:boctulus/llm-providers.git'
    }
}

$definition = $packageDefinitions[$Package]
if ($Push -and $Strategy -eq 'FilterRepo') {
    throw '-Push is not available with the disposable FilterRepo strategy. Use the Subtree strategy only in an explicitly authorized package publication workflow.'
}

$repositoryRoot = (git rev-parse --show-toplevel).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($repositoryRoot)) {
    throw 'Run this script from a SimpleRest Git working tree.'
}

$resolvedCommit = (git rev-parse "$SourceCommit^{commit}").Trim()
if ($LASTEXITCODE -ne 0 -or $resolvedCommit -notmatch '^[0-9a-f]{40}$') {
    throw "SourceCommit '$SourceCommit' does not resolve to an immutable Git commit."
}

$sourceTree = (& git rev-parse "$($resolvedCommit):$($definition.Path)").Trim()
if ($LASTEXITCODE -ne 0 -or $sourceTree -notmatch '^[0-9a-f]{40}$') {
    throw "Package path '$($definition.Path)' does not resolve to a tree at $resolvedCommit."
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

$splitCommit = $null
$splitMethod = $Strategy

if ($Strategy -eq 'Subtree') {
    $splitOutput = & git subtree split -q "--prefix=$($definition.Path)" $resolvedCommit
    if ($LASTEXITCODE -ne 0) {
        throw "git subtree split failed for '$Package' at $resolvedCommit."
    }

    $subtreeCommits = @($splitOutput | Where-Object { $_ -match '^[0-9a-f]{40}$' } | Select-Object -Last 1)
    if ($subtreeCommits.Count -ne 1) {
        throw "git subtree split did not return a package commit for '$Package'."
    }
    $splitCommit = $subtreeCommits[0]
} else {
    $filterRepoVersion = & git filter-repo --version 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "git filter-repo is required for the disposable-clone split strategy."
    }

    $tempRoot = [System.IO.Path]::GetFullPath([System.IO.Path]::GetTempPath()).TrimEnd([System.IO.Path]::DirectorySeparatorChar)
    $clonePath = Join-Path $tempRoot ("simplerest-package-split-" + [guid]::NewGuid().ToString('N'))
    try {
        & git clone --shared --no-checkout $repositoryRoot $clonePath
        if ($LASTEXITCODE -ne 0) {
            throw "Could not create a disposable clone for '$Package'."
        }

        & git -C $clonePath update-ref refs/heads/split-source $resolvedCommit
        if ($LASTEXITCODE -ne 0) {
            throw "Could not create the source ref '$resolvedCommit' in the disposable clone."
        }
        & git -C $clonePath symbolic-ref HEAD refs/heads/split-source
        if ($LASTEXITCODE -ne 0) {
            throw "Could not select the package source ref in the disposable clone."
        }

        & git -C $clonePath filter-repo --force --refs refs/heads/split-source --path "$($definition.Path)/" --path-rename "$($definition.Path)/:"
        if ($LASTEXITCODE -ne 0) {
            throw "git filter-repo failed for '$Package' in the disposable clone."
        }

        $splitCommit = (& git -C $clonePath rev-parse refs/heads/split-source).Trim()
        if ($LASTEXITCODE -ne 0 -or $splitCommit -notmatch '^[0-9a-f]{40}$') {
            throw "Could not resolve the filtered package commit for '$Package'."
        }

        $filteredTree = (& git -C $clonePath rev-parse 'refs/heads/split-source^{tree}').Trim()
        if ($LASTEXITCODE -ne 0 -or $filteredTree -ne $sourceTree) {
            throw "Filtered package tree '$filteredTree' does not exactly match source package tree '$sourceTree'."
        }

        $filteredManifest = & git -C $clonePath show 'refs/heads/split-source:composer.json' 2>$null
        if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace(($filteredManifest -join ''))) {
            throw "Filtered package history for '$Package' does not contain composer.json at its root."
        }
        $filteredPackage = ($filteredManifest -join "`n") | ConvertFrom-Json
        if ($filteredPackage.name -ne $Package) {
            throw "Filtered package root declares '$($filteredPackage.name)' instead of '$Package'."
        }

        & composer --working-dir=$clonePath validate --strict --no-check-publish --no-interaction
        if ($LASTEXITCODE -ne 0) {
            throw "Filtered composer.json failed strict validation for '$Package'."
        }

        $splitTreeFiles = @(& git -C $clonePath ls-tree -r --name-only refs/heads/split-source)
        if ($LASTEXITCODE -ne 0 -or $splitTreeFiles.Count -eq 0) {
            throw "Could not inspect the filtered package tree for '$Package'."
        }
        $splitMethod = "FilterRepo ($($filterRepoVersion -join ''))"
        Write-Output "Tree hash:     $filteredTree (matches source path tree)"

        if ($PersistSplit) {
            $persistedRef = "refs/package-splits/$($definition.Repository)"
            & git -C $repositoryRoot show-ref --verify --quiet $persistedRef
            if ($LASTEXITCODE -eq 0) {
                throw "Local split ref '$persistedRef' already exists; refusing to replace it."
            }

            & git -C $repositoryRoot fetch --no-tags --no-write-fetch-head $clonePath "refs/heads/split-source:$persistedRef"
            if ($LASTEXITCODE -ne 0) {
                throw "Could not persist the validated split commit for '$Package'."
            }

            $persistedCommit = (& git -C $repositoryRoot rev-parse $persistedRef).Trim()
            if ($LASTEXITCODE -ne 0 -or $persistedCommit -ne $splitCommit) {
                throw "Persisted ref '$persistedRef' does not point to the validated split commit."
            }
            Write-Output "Persisted ref: $persistedRef"
        }
    } finally {
        if (Test-Path -LiteralPath $clonePath) {
            $resolvedClonePath = [System.IO.Path]::GetFullPath((Resolve-Path -LiteralPath $clonePath).Path)
            $tempPrefix = $tempRoot + [System.IO.Path]::DirectorySeparatorChar
            $isInsideTemp = $resolvedClonePath.StartsWith($tempPrefix, [System.StringComparison]::OrdinalIgnoreCase)
            $hasExpectedName = [System.IO.Path]::GetFileName($resolvedClonePath).StartsWith('simplerest-package-split-', [System.StringComparison]::Ordinal)
            if (-not $isInsideTemp -or -not $hasExpectedName) {
                throw "Refusing to remove an unverified disposable clone path: $resolvedClonePath"
            }
            Remove-Item -LiteralPath $resolvedClonePath -Recurse -Force
        }
    }
}

Write-Output "Package:       $Package"
Write-Output "Source commit: $resolvedCommit"
Write-Output "Split method:  $splitMethod"
Write-Output "Package tree:  $splitCommit"
Write-Output "Target repo:   $($definition.Repository)"

if (-not $Push) {
    if ($PersistSplit) {
        Write-Output 'Split persisted locally; no remote ref was changed.'
    } else {
        Write-Output 'Dry run only. Re-run with -Push after package tests and release review pass.'
    }
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

git push $definition.Remote "$splitCommit`:refs/heads/main"
if ($LASTEXITCODE -ne 0) {
    throw "Could not push the package split to '$($definition.Repository)' main. No force push was attempted."
}

if ($Version) {
    git push $definition.Remote "$splitCommit`:refs/tags/$Version"
    if ($LASTEXITCODE -ne 0) {
        throw "Could not create new tag '$Version' in '$($definition.Repository)'."
    }
}

Write-Output 'Package split published without force-updating any ref.'

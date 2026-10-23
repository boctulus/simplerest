[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [string]$GoPropPath
)

$ErrorActionPreference = 'Stop'

$goPropRoot = [System.IO.Path]::GetFullPath($GoPropPath)
$goPropManifestPath = Join-Path $goPropRoot 'composer.json'
if (-not (Test-Path -LiteralPath $goPropManifestPath -PathType Leaf)) {
    throw "GoProp composer.json was not found at '$goPropRoot'."
}

$branch = (& git -C $goPropRoot rev-parse --abbrev-ref HEAD).Trim()
if ($LASTEXITCODE -ne 0 -or $branch -ne 'dev') {
    throw "Expected GoProp branch 'dev'; found '$branch'. This check is read-only and will not switch branches."
}
$goPropCommit = (& git -C $goPropRoot rev-parse HEAD).Trim()
if ($LASTEXITCODE -ne 0 -or $goPropCommit -notmatch '^[0-9a-f]{40}$') {
    throw 'Could not resolve the GoProp source commit.'
}

$simpleRestRoot = (& git rev-parse --show-toplevel).Trim()
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($simpleRestRoot)) {
    throw 'Run this script from a SimpleRest Git working tree.'
}
$simpleRestRoot = [System.IO.Path]::GetFullPath($simpleRestRoot)
$bootstrapHtmlPath = Join-Path $simpleRestRoot 'packages/boctulus/simplerest-bootstrap-html'
$webInspectionPath = Join-Path $simpleRestRoot 'packages/boctulus/simplerest-web-inspection'

$autoloadFiles = @(
    'src/framework/Api/ApiController.php',
    'src/framework/Api/AuthController.php',
    'src/framework/Api/Collections.php',
    'src/framework/Libs/RedisLock.php',
    'src/framework/Response.php'
)
$goPropManifest = Get-Content -LiteralPath $goPropManifestPath -Raw | ConvertFrom-Json
$declaredFiles = @($goPropManifest.autoload.files)
if (($declaredFiles -join [Environment]::NewLine) -ne ($autoloadFiles -join [Environment]::NewLine)) {
    throw "GoProp's autoload.files no longer matches the five audited framework overrides. Review this fixture before running it."
}

foreach ($relativePath in $autoloadFiles) {
    if (-not (Test-Path -LiteralPath (Join-Path $goPropRoot $relativePath) -PathType Leaf)) {
        throw "GoProp autoload file is missing: $relativePath"
    }
}

$tempRoot = [System.IO.Path]::GetFullPath([System.IO.Path]::GetTempPath()).TrimEnd([System.IO.Path]::DirectorySeparatorChar)
$runRoot = Join-Path $tempRoot ('simplerest-goprop-consumer-' + [guid]::NewGuid().ToString('N'))
if (-not (New-Item -ItemType Directory -Path $runRoot)) {
    throw "Could not create temporary GoProp consumer directory: $runRoot"
}

function Assert-And-RemoveTempTree([string]$TargetPath) {
    if (-not (Test-Path -LiteralPath $TargetPath)) {
        return
    }
    $resolvedTarget = [System.IO.Path]::GetFullPath((Resolve-Path -LiteralPath $TargetPath).Path)
    $tempPrefix = $tempRoot + [System.IO.Path]::DirectorySeparatorChar
    $isInsideTemp = $resolvedTarget.StartsWith($tempPrefix, [System.StringComparison]::OrdinalIgnoreCase)
    $hasExpectedName = [System.IO.Path]::GetFileName($resolvedTarget).StartsWith('simplerest-goprop-consumer-', [System.StringComparison]::Ordinal)
    if (-not $isInsideTemp -or -not $hasExpectedName) {
        throw "Refusing to remove an unverified temporary consumer path: $resolvedTarget"
    }
    Remove-Item -LiteralPath $resolvedTarget -Recurse -Force
}

function New-PathRepository([string]$PackageName, [string]$Path, [string]$Version) {
    return @{
        type = 'path'
        url = $Path.Replace('\', '/')
        options = @{
            symlink = $false
            versions = @{ $PackageName = $Version }
        }
        canonical = $true
    }
}

function Invoke-ComposerConsumer([string]$ConsumerPath, [bool]$IncludeHtmlBuilders) {
    $manifest = Get-Content -LiteralPath $goPropManifestPath -Raw | ConvertFrom-Json
    $manifest.require.'boctulus/simplerest' = '^3.0'
    if ($IncludeHtmlBuilders) {
        $manifest.require | Add-Member -NotePropertyName 'boctulus/simplerest-bootstrap-html' -NotePropertyValue '^1.0' -Force
        $manifest.require | Add-Member -NotePropertyName 'boctulus/simplerest-web-inspection' -NotePropertyValue '^1.0' -Force
    }

    $repositories = @(
        (New-PathRepository 'boctulus/simplerest' $simpleRestRoot '3.0.0'),
        (New-PathRepository 'boctulus/simplerest-bootstrap-html' $bootstrapHtmlPath '1.0.0'),
        (New-PathRepository 'boctulus/simplerest-web-inspection' $webInspectionPath '1.0.0')
    ) + @($manifest.repositories)
    $manifest.repositories = $repositories
    $manifest.PSObject.Properties.Remove('require-dev')
    $manifest.PSObject.Properties.Remove('scripts')
    $manifest.PSObject.Properties.Remove('autoload-dev')
    $manifest.config.'optimize-autoloader' = $true

    $composerJson = ConvertTo-Json -InputObject $manifest -Depth 100
    [System.IO.File]::WriteAllText(
        (Join-Path $ConsumerPath 'composer.json'),
        $composerJson + [Environment]::NewLine,
        [System.Text.UTF8Encoding]::new($false)
    )

    foreach ($relativePath in $autoloadFiles) {
        $destination = Join-Path $ConsumerPath $relativePath
        $destinationDirectory = Split-Path -Parent $destination
        New-Item -ItemType Directory -Path $destinationDirectory -Force | Out-Null
        Copy-Item -LiteralPath (Join-Path $goPropRoot $relativePath) -Destination $destination
    }

    Push-Location $ConsumerPath
    try {
        composer update --no-dev --no-scripts --no-plugins --no-interaction --no-progress
        if ($LASTEXITCODE -ne 0) {
            throw "Composer could not resolve the GoProp consumer fixture (HTML Builders: $IncludeHtmlBuilders)."
        }
        composer check-platform-reqs --no-dev
        if ($LASTEXITCODE -ne 0) {
            throw "Platform requirements failed for the GoProp consumer fixture (HTML Builders: $IncludeHtmlBuilders)."
        }

        $smokePath = Join-Path $ConsumerPath 'goprop-consumer-smoke.php'
        $smokeSource = @'
<?php
declare(strict_types=1);
$root = __DIR__ . DIRECTORY_SEPARATOR;
define('ROOT_PATH', $root);
define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', $root . 'config' . DIRECTORY_SEPARATOR);
define('ETC_PATH', $root . 'etc' . DIRECTORY_SEPARATOR);
@mkdir(CONFIG_PATH, 0777, true);
file_put_contents(CONFIG_PATH . 'config.php', "<?php return ['namespace' => 'GoPropConsumerSmoke'];");
require $root . 'vendor/autoload.php';

$overrides = [
    'Boctulus\\Simplerest\\Core\\Api\\ApiController' => 'src/framework/Api/ApiController.php',
    'Boctulus\\Simplerest\\Core\\Api\\AuthController' => 'src/framework/Api/AuthController.php',
    'Boctulus\\Simplerest\\Core\\Api\\Collections' => 'src/framework/Api/Collections.php',
    'Boctulus\\Simplerest\\Core\\Libs\\RedisLock' => 'src/framework/Libs/RedisLock.php',
    'Boctulus\\Simplerest\\Core\\Response' => 'src/framework/Response.php',
];
foreach ($overrides as $class => $relativePath) {
    if (!class_exists($class)) {
        fwrite(STDERR, "GoProp override did not load: {$class}\n");
        exit(1);
    }
    $actualFile = realpath((new ReflectionClass($class))->getFileName());
    $expectedFile = realpath($root . $relativePath);
    if ($actualFile === false || $actualFile !== $expectedFile) {
        fwrite(STDERR, "Unexpected class origin for {$class}: " . ($actualFile ?: 'missing') . "\n");
        exit(1);
    }
}

$legacyBuilder = 'Boctulus\\Simplerest\\Core\\Libs\\HtmlBuilder\\Bt5Form';
$mode = $argv[1] ?? '';
try {
    $builderAvailable = class_exists($legacyBuilder);
} catch (LogicException $error) {
    if ($mode !== 'without-html-builders'
        || !str_contains($error->getMessage(), 'boctulus/simplerest-bootstrap-html')) {
        fwrite(STDERR, "Unexpected legacy Bt5Form resolution error: {$error->getMessage()}\n");
        exit(1);
    }
    fwrite(STDOUT, "EXPECTED BLOCKER: GoProp's current manifest lacks boctulus/simplerest-bootstrap-html.\n");
    fwrite(STDOUT, "PASS five local override classes loaded from GoProp files without redeclaration.\n");
    exit(0);
}

if ($mode === 'without-html-builders') {
    fwrite(STDERR, "GoProp's current manifest unexpectedly resolved Bt5Form without the Bootstrap HTML package.\n");
    exit(1);
}
if (!$builderAvailable || !class_exists('Boctulus\\Simplerest\\HtmlBuilders\\Bt5Form')) {
    fwrite(STDERR, "Bt5Form did not resolve after installing SimpleRest Bootstrap HTML.\n");
    exit(1);
}
$legacyCrawler = 'Boctulus\\Simplerest\\Core\\Libs\\DomCrawler';
if (!class_exists($legacyCrawler) || !class_exists('Boctulus\\Simplerest\\Integrations\\Libs\\DomCrawler')) {
    fwrite(STDERR, "GoProp's DomCrawler wrapper did not resolve after installing SimpleRest Web Inspection.\n");
    exit(1);
}
$builder = new ReflectionClass($legacyBuilder);
if ($builder->getName() !== 'Boctulus\\Simplerest\\HtmlBuilders\\Bt5Form') {
    fwrite(STDERR, "Legacy Bt5Form alias resolved to an unexpected class identity.\n");
    exit(1);
}
fwrite(STDOUT, "PASS five local override classes loaded from GoProp files without redeclaration.\n");
fwrite(STDOUT, "PASS legacy Bt5Form resolved through boctulus/simplerest-bootstrap-html.\n");
fwrite(STDOUT, "PASS legacy DomCrawler resolved through boctulus/simplerest-web-inspection.\n");
'@
        [System.IO.File]::WriteAllText($smokePath, $smokeSource, [System.Text.UTF8Encoding]::new($false))
        $mode = if ($IncludeHtmlBuilders) { 'with-html-builders' } else { 'without-html-builders' }
        php $smokePath $mode
        if ($LASTEXITCODE -ne 0) {
            throw "GoProp consumer smoke failed (HTML Builders: $IncludeHtmlBuilders)."
        }
    } finally {
        Pop-Location
    }
}

try {
    Write-Output "GoProp branch/commit: $branch / $goPropCommit"
    Write-Output "Core requirement in fixture: $($goPropManifest.require.'boctulus/simplerest') -> ^3.0 (temporary consumer only)"

    $withoutHtml = Join-Path $runRoot 'without-html-builders'
    $withHtml = Join-Path $runRoot 'with-html-builders'
    New-Item -ItemType Directory -Path $withoutHtml | Out-Null
    New-Item -ItemType Directory -Path $withHtml | Out-Null

    Invoke-ComposerConsumer $withoutHtml $false
    Invoke-ComposerConsumer $withHtml $true
    Write-Output 'GoProp consumer compatibility fixture completed. GoProp files were read only; all installs used disposable temporary consumers.'
} finally {
    Assert-And-RemoveTempTree $runRoot
}

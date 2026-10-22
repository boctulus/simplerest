<?php

declare(strict_types=1);

/**
 * Inspect the files consumers receive from Composer and the application packer.
 *
 * Run from any directory with:
 *   php scripts/distribution/verify-release-artifacts.php
 *   php scripts/distribution/verify-release-artifacts.php --with-consumer-install
 *   php scripts/distribution/verify-release-artifacts.php --self-test
 */

$root = dirname(__DIR__, 2);
$withConsumerInstall = in_array('--with-consumer-install', $argv, true);

/** @return list<string> */
function detectDistributionViolations(string $path, ?string $contents = null): array
{
    $normalized = strtolower(str_replace('\\', '/', trim($path, '/')));
    $segments = array_filter(explode('/', $normalized), static fn(string $segment): bool => $segment !== '');
    $violations = [];

    foreach (['.git', 'vendor', 'node_modules', 'third_party'] as $blockedDirectory) {
        if (in_array($blockedDirectory, $segments, true)) {
            $violations[] = "copied dependency directory: {$path}";
        }
    }

    if (preg_match(
        '~(^|/)(jquery(?:[-.]?[0-9][^/]*)?(?:\.min)?\.js|bootstrap(?:[-.]?v?[0-9][^/]*)?(?:\.min)?\.(?:js|css)|tailwind(?:\.min)?\.css|axios(?:\.min)?\.js|select2(?:\.min)?\.(?:js|css)|adminlte(?:\.min)?\.(?:js|css)|popper(?:\.min)?\.js|font-awesome(?:/|\.|$)|fontawesome(?:\.min)?\.css)$~i',
        $normalized
    )) {
        $violations[] = "known third-party frontend asset: {$path}";
    }

    $basename = basename($normalized);
    if (in_array($basename, ['simplexlsx.php', 'qrcode.php', 'phpqrcode.php'], true)) {
        $violations[] = "known copied third-party PHP library: {$path}";
    }

    if ($contents !== null && preg_match(
        '~(?:jQuery JavaScript Library|Bootstrap v[0-9]|Tailwind CSS v[0-9]|namespace\s+chillerlan\\\\|class\s+SimpleXLSX\b|class\s+QRcode\b)~i',
        $contents
    )) {
        $violations[] = "known third-party source signature: {$path}";
    }

    return array_values(array_unique($violations));
}

function assertDetectorSelfTest(): void
{
    $badCases = [
        ['.git', 'gitdir: C:/private/worktree/.git/worktrees/simplerest'],
        ['public/assets/third_party/bootstrap/bootstrap.min.js', ''],
        ['src/SimpleXLSX.php', '<?php class SimpleXLSX {}'],
        ['assets/jquery.min.js', '/*! jQuery JavaScript Library */'],
    ];

    foreach ($badCases as [$path, $contents]) {
        if (detectDistributionViolations($path, $contents) === []) {
            throw new RuntimeException("Distribution detector failed to reject fixture: {$path}");
        }
    }

    if (detectDistributionViolations('src/framework/Libs/Request.php', '<?php class Request {}') !== []) {
        throw new RuntimeException('Distribution detector rejected a first-party framework file.');
    }

    $fixtureDirectory = createTemporaryDirectory();
    $gitMarker = $fixtureDirectory . DIRECTORY_SEPARATOR . '.git';
    $readOnlyFile = $fixtureDirectory . DIRECTORY_SEPARATOR . 'readonly-fixture.txt';
    $gitMarkerRejected = false;
    try {
        file_put_contents($gitMarker, 'gitdir: C:/private/worktree/.git/worktrees/simplerest');
        file_put_contents($readOnlyFile, 'cleanup guard fixture');
        if (!chmod($readOnlyFile, 0444)) {
            throw new RuntimeException('Could not make the cleanup fixture read-only.');
        }
        try {
            assertNoArchiveSuppliedGitMarker($fixtureDirectory);
        } catch (RuntimeException $error) {
            if (!str_starts_with($error->getMessage(), 'Refusing to initialize a temporary VCS repository')) {
                throw $error;
            }
            $gitMarkerRejected = true;
        }
        if (!$gitMarkerRejected) {
            throw new RuntimeException('Temporary VCS safety guard accepted an archive-supplied .git marker.');
        }
    } finally {
        removeTemporaryDirectory($fixtureDirectory);
    }

    fwrite(STDOUT, "Distribution gate self-test passed (4 rejected artifacts, 1 allowed framework file, .git marker guard).\n");
}

function runCommand(array $arguments, string $workingDirectory): string
{
    $binary = getenv('COMPOSER_BINARY') ?: 'composer';
    if (getenv('COMPOSER_BINARY') === false && PHP_OS_FAMILY === 'Windows') {
        $composerCandidates = [];
        exec('where.exe composer.bat 2>NUL', $composerCandidates, $whereExitCode);
        if ($whereExitCode === 0 && isset($composerCandidates[0])) {
            $binary = trim($composerCandidates[0]);
        }
    }
    return runExecutable(array_merge([$binary], $arguments), $workingDirectory);
}

function runExecutable(array $arguments, string $workingDirectory): string
{
    if ($arguments === []) {
        throw new RuntimeException('Cannot run an empty command.');
    }

    $command = implode(' ', array_map('escapeshellarg', $arguments)) . ' 2>&1';
    $previousDirectory = getcwd();

    if (!chdir($workingDirectory)) {
        throw new RuntimeException("Cannot enter working directory: {$workingDirectory}");
    }

    try {
        exec($command, $output, $exitCode);
    } finally {
        if ($previousDirectory !== false) {
            chdir($previousDirectory);
        }
    }

    if ($exitCode !== 0) {
        throw new RuntimeException("Command failed ({$exitCode}): {$command}\n" . implode("\n", $output));
    }

    return implode("\n", $output);
}

function runGitCommand(array $arguments, string $workingDirectory): string
{
    $command = implode(' ', array_map('escapeshellarg', array_merge(['git'], $arguments))) . ' 2>&1';
    $previousDirectory = getcwd();
    if (!chdir($workingDirectory)) {
        throw new RuntimeException("Cannot enter Git working directory: {$workingDirectory}");
    }

    try {
        exec($command, $output, $exitCode);
    } finally {
        if ($previousDirectory !== false) {
            chdir($previousDirectory);
        }
    }

    if ($exitCode !== 0) {
        throw new RuntimeException("Git command failed ({$exitCode}): {$command}\n" . implode("\n", $output));
    }

    return implode("\n", $output);
}

function scanArchive(string $archivePath, string $artifactLabel): array
{
    $zip = new ZipArchive();
    if ($zip->open($archivePath) !== true) {
        throw new RuntimeException("Cannot open Composer archive: {$archivePath}");
    }

    $violations = [];
    $fileCount = 0;
    $expandedBytes = 0;
    for ($index = 0; $index < $zip->numFiles; $index++) {
        $entry = $zip->getNameIndex($index);
        if ($entry === false || str_ends_with($entry, '/')) {
            continue;
        }

        $fileCount++;
        $stat = $zip->statIndex($index);
        $expandedBytes += (int) ($stat['size'] ?? 0);
        $contents = $zip->getFromIndex($index);
        $violations = array_merge($violations, detectDistributionViolations($entry, $contents === false ? null : $contents));
    }
    $zip->close();

    if ($violations !== []) {
        throw new RuntimeException("{$artifactLabel} contains prohibited third-party payloads:\n- " . implode("\n- ", array_unique($violations)));
    }

    return ['files' => $fileCount, 'bytes' => filesize($archivePath) ?: 0, 'expandedBytes' => $expandedBytes];
}

function scanDirectory(string $directory, string $artifactLabel): array
{
    $violations = [];
    $fileCount = 0;
    $bytes = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $item) {
        if (!$item->isFile()) {
            continue;
        }

        $relativePath = substr($item->getPathname(), strlen($directory) + 1);
        $contents = file_get_contents($item->getPathname());
        $violations = array_merge($violations, detectDistributionViolations($relativePath, $contents === false ? null : $contents));
        $fileCount++;
        $bytes += $item->getSize();
    }

    if ($violations !== []) {
        throw new RuntimeException("{$artifactLabel} contains prohibited third-party payloads:\n- " . implode("\n- ", array_unique($violations)));
    }

    return ['files' => $fileCount, 'bytes' => $bytes];
}

function createTemporaryDirectory(): string
{
    $tempRoot = realpath(sys_get_temp_dir());
    if ($tempRoot === false) {
        throw new RuntimeException('Cannot resolve the system temporary directory.');
    }

    $directory = $tempRoot . DIRECTORY_SEPARATOR . 'simplerest-distribution-' . bin2hex(random_bytes(8));
    if (!mkdir($directory, 0700)) {
        throw new RuntimeException("Cannot create temporary directory: {$directory}");
    }

    return $directory;
}

function removeTemporaryDirectory(string $directory): void
{
    $realPath = realpath($directory);
    $tempRoot = realpath(sys_get_temp_dir());
    if ($realPath === false || $tempRoot === false || dirname($realPath) !== $tempRoot
        || !str_starts_with(basename($realPath), 'simplerest-distribution-')) {
        throw new RuntimeException("Refusing to remove an unverified temporary path: {$directory}");
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($realPath, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        $path = $item->getPathname();
        if ($item->isDir() && !$item->isLink()) {
            @chmod($path, 0777);
            if (!@rmdir($path)) {
                throw new RuntimeException("Cannot remove temporary directory entry: {$path}");
            }
        } else {
            if (!$item->isLink()) {
                @chmod($path, 0666);
            }
            if (!@unlink($path)) {
                throw new RuntimeException("Cannot remove temporary file entry: {$path}");
            }
        }
    }
    if (!@rmdir($realPath)) {
        throw new RuntimeException("Cannot remove temporary directory: {$realPath}");
    }
}

function verifyStarter(string $root, string $starter): array
{
    if (!is_file($root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
        throw new RuntimeException('Install root development dependencies before verifying the starter.');
    }

    require_once $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    require_once $root . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'pack_framework.php';

    ob_start();
    $packager = new SimpleRestPackager($root, $starter);
    $success = $packager->run(true, true);
    $packerOutput = (string) ob_get_clean();
    if (!$success) {
        throw new RuntimeException("Application starter generation failed:\n{$packerOutput}");
    }

    foreach ([
        'app.php',
        'composer.json',
        'config/config.php',
        'scripts/init/boot/boot.php',
        'scripts/init/redirection',
        'app/Views/home.php',
    ] as $requiredPath) {
        $absolutePath = $starter . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $requiredPath);
        if (!file_exists($absolutePath)) {
            throw new RuntimeException("Generated starter is missing required path: {$requiredPath}");
        }
    }

    $homeView = file_get_contents($starter . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'home.php');
    if ($homeView === false || stripos($homeView, 'bootstrap') !== false || stripos($homeView, 'jquery') !== false || stripos($homeView, 'tailwind') !== false) {
        throw new RuntimeException('Generated starter welcome view must not require a copied frontend library.');
    }

    return scanDirectory($starter, 'Generated application starter');
}

function createLocalVcsRepository(string $archivePath, string $repositoryDirectory, ?string $branchAlias = null): string
{
    if (!mkdir($repositoryDirectory, 0700, true)) {
        throw new RuntimeException("Cannot create isolated package repository: {$repositoryDirectory}");
    }

    $zip = new ZipArchive();
    if ($zip->open($archivePath) !== true || !$zip->extractTo($repositoryDirectory)) {
        throw new RuntimeException("Cannot extract Composer artifact into isolated VCS repository: {$archivePath}");
    }
    $zip->close();

    $gitMarker = $repositoryDirectory . DIRECTORY_SEPARATOR . '.git';
    assertNoArchiveSuppliedGitMarker($repositoryDirectory);

    if ($branchAlias !== null) {
        $manifestPath = $repositoryDirectory . DIRECTORY_SEPARATOR . 'composer.json';
        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $manifest['extra']['branch-alias']['dev-main'] = $branchAlias;
        file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL);
    }

    runGitCommand(['init', '--quiet', '--initial-branch=main'], $repositoryDirectory);
    $gitDirectory = trim(runGitCommand(['rev-parse', '--absolute-git-dir'], $repositoryDirectory));
    $expectedGitDirectory = realpath($gitMarker);
    $normalizePath = static fn(string $path): string => rtrim(str_replace('\\', '/', $path), '/');
    if ($expectedGitDirectory === false
        || strcasecmp($normalizePath($gitDirectory), $normalizePath($expectedGitDirectory)) !== 0) {
        throw new RuntimeException(
            "Temporary Composer fixture resolved Git metadata outside its isolated repository: {$gitDirectory}"
        );
    }
    runGitCommand(['config', 'user.name', 'SimpleRest Distribution Gate'], $repositoryDirectory);
    runGitCommand(['config', 'user.email', 'distribution-gate@example.invalid'], $repositoryDirectory);
    runGitCommand(['config', 'core.hooksPath', $repositoryDirectory . DIRECTORY_SEPARATOR . 'no-hooks'], $repositoryDirectory);
    runGitCommand(['add', '--all', '--force'], $repositoryDirectory);
    runGitCommand(['commit', '--quiet', '-m', 'Temporary Composer package fixture'], $repositoryDirectory);

    $path = str_replace('\\', '/', realpath($repositoryDirectory) ?: $repositoryDirectory);
    if (preg_match('~^[A-Za-z]:/~', $path)) {
        return 'file:///' . str_replace(' ', '%20', $path);
    }
    return 'file://' . str_replace(' ', '%20', $path);
}

function assertNoArchiveSuppliedGitMarker(string $repositoryDirectory): void
{
    $gitMarker = $repositoryDirectory . DIRECTORY_SEPARATOR . '.git';
    if (file_exists($gitMarker) || is_link($gitMarker)) {
        throw new RuntimeException(
            "Refusing to initialize a temporary VCS repository over an archive-supplied .git marker: {$gitMarker}"
        );
    }
}

function packageRepositoryEntries(array $repositories): array
{
    $entries = [];
    foreach ($repositories as $url) {
        $entries[] = ['type' => 'vcs', 'url' => $url, 'canonical' => true];
    }
    return $entries;
}

function writeConsumerManifest(
    string $consumerDirectory,
    array $repositories,
    array $requirements,
    bool $optimizeAutoloader = false,
    array $devRequirements = []
): void
{
    $consumerComposer = [
        'name' => 'boctulus/distribution-smoke-test',
        'type' => 'project',
        'repositories' => packageRepositoryEntries($repositories),
        'require' => $requirements,
        'minimum-stability' => 'dev',
        'prefer-stable' => true,
        'config' => [
            'allow-plugins' => false,
            'optimize-autoloader' => $optimizeAutoloader,
            'sort-packages' => true,
        ],
    ];

    if ($devRequirements !== []) {
        $consumerComposer['require-dev'] = $devRequirements;
    }

    file_put_contents(
        $consumerDirectory . DIRECTORY_SEPARATOR . 'composer.json',
        json_encode($consumerComposer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL
    );
}

function installVcsConsumer(string $consumerDirectory, bool $checkPsr4 = false, bool $withDev = false): void
{
    // The consumer's first-party packages have only the explicit local VCS
    // repositories below; prefer Packagist ZIPs for transitive dependencies.
    // Packages with no dist artifact still install from their VCS source.
    $installArguments = ['install', '--prefer-dist', '--no-scripts', '--no-plugins', '--no-interaction', '--no-progress'];
    if (!$withDev) {
        $installArguments[] = '--no-dev';
    }
    $installOutput = runCommand($installArguments, $consumerDirectory);
    if ($checkPsr4 && preg_match('/does not comply with psr-4/i', $installOutput)) {
        throw new RuntimeException("External Composer consumer reported PSR-4 autoload mismatches:\n{$installOutput}");
    }
    $platformArguments = ['check-platform-reqs'];
    if (!$withDev) {
        $platformArguments[] = '--no-dev';
    }
    runCommand($platformArguments, $consumerDirectory);
}

function runAutoloadSmoke(string $consumerDirectory, array $classes, array $traits = [], bool $discoverCodeToolsCommands = false): void
{
    $smokeFile = $consumerDirectory . DIRECTORY_SEPARATOR . 'autoload-smoke.php';
    $smokeSource = <<<'PHP'
<?php
$rootPath = __DIR__ . DIRECTORY_SEPARATOR;
define('ROOT_PATH', $rootPath);
define('APP_PATH', $rootPath . 'app' . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', $rootPath . 'config' . DIRECTORY_SEPARATOR);
define('CORE_PATH', $rootPath . 'vendor' . DIRECTORY_SEPARATOR . 'boctulus' . DIRECTORY_SEPARATOR . 'simplerest' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR);
define('COMMANDS_PATH', APP_PATH . 'Commands' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', $rootPath . 'vendor' . DIRECTORY_SEPARATOR);
define('PACKAGES_PATH', $rootPath . 'packages' . DIRECTORY_SEPARATOR);
@mkdir(CONFIG_PATH, 0777, true);
@mkdir(COMMANDS_PATH, 0777, true);
file_put_contents(CONFIG_PATH . 'config.php', "<?php return ['namespace' => 'DistributionSmoke'];");
file_put_contents(CONFIG_PATH . 'databases.php', '<?php return [];');
require __DIR__ . '/vendor/autoload.php';
$classes = __EXPECTED_CLASSES__;
$traits = __EXPECTED_TRAITS__;
foreach ($classes as $class) {
    if (!class_exists($class)) {
        fwrite(STDERR, "Composer autoload did not resolve {$class}\n");
        exit(1);
    }
}
foreach ($traits as $trait) {
    if (!trait_exists($trait)) {
        fwrite(STDERR, "Composer autoload did not resolve {$trait}\n");
        exit(1);
    }
}
if (__DISCOVER_CODE_TOOLS__) {
    @mkdir(__DIR__ . '/app/Commands', 0777, true);
    $registry = new Boctulus\Simplerest\Core\Libs\CommandRegistry(false, [
        'application_path' => __DIR__ . '/app/Commands',
        'vendor_path' => __DIR__ . '/vendor',
        'packages' => ['boctulus/simplerest-code-tools'],
    ]);
    $registry->loadGroups();
    if (!$registry->hasGroup('make')) {
        fwrite(STDERR, "SimpleRest command discovery did not register the Code Tools make group.\n");
        exit(1);
    }
    fwrite(STDOUT, "Discovered the Code Tools make command group.\n");
}
fwrite(STDOUT, 'Resolved ' . count($classes) . ' consumer classes and ' . count($traits) . " traits.\n");
PHP;
    $smokeSource = str_replace('__EXPECTED_CLASSES__', var_export($classes, true), $smokeSource);
    $smokeSource = str_replace('__EXPECTED_TRAITS__', var_export($traits, true), $smokeSource);
    $smokeSource = str_replace('__DISCOVER_CODE_TOOLS__', $discoverCodeToolsCommands ? 'true' : 'false', $smokeSource);
    file_put_contents($smokeFile, $smokeSource);
    runExecutable([PHP_BINARY, $smokeFile], $consumerDirectory);
}

function installExternalVcsConsumers(array $packageManifests, array $repositories, string $consumerDirectory, string $temporaryDirectory): void
{
    $requiredPackages = ['boctulus/simplerest' => '2.0.x-dev'];
    $providers = [];
    foreach ($packageManifests as $manifest) {
        $package = json_decode((string) file_get_contents($manifest), true, 512, JSON_THROW_ON_ERROR);
        $requiredPackages[$package['name']] = 'dev-main';
        foreach ($package['extra']['simplerest']['providers'] ?? [] as $provider) {
            $providers[] = $provider;
        }
    }

    writeConsumerManifest($consumerDirectory, $repositories, $requiredPackages);
    installVcsConsumer($consumerDirectory);
    runAutoloadSmoke($consumerDirectory, array_merge([
        'Boctulus\\Simplerest\\Core\\Request',
        'Boctulus\\Simplerest\\Core\\Libs\\PdfBase',
        'Boctulus\\Simplerest\\CodeTools\\Cleaners\\BootstrapCleaner',
        'Boctulus\\Simplerest\\Core\\Libs\\code_cleaner\\BootstrapCleaner',
        'Boctulus\\Simplerest\\Core\\Libs\\PHPParser',
        'Boctulus\\OpenfacturaSdk\\Factory\\OpenFacturaSDKFactory',
        'Boctulus\\FriendlyposWeb\\Controllers\\OpenFacturaController',
    ], $providers), ['Boctulus\\ShopifyConnector\\Traits\\ShopifyWooCommerceAdaptorTrait'], true);

    $candidates = [
        [
            'name' => 'boctulus/simplerest-pdf',
            'classes' => ['Boctulus\\Simplerest\\Core\\Libs\\PdfBase'],
        ],
        [
            'name' => 'boctulus/simplerest-code-tools',
            'classes' => [
                'Boctulus\\Simplerest\\Core\\Libs\\PHPParser',
                'Boctulus\\Simplerest\\CodeTools\\Cleaners\\BootstrapCleaner',
                'Boctulus\\Simplerest\\Core\\Libs\\code_cleaner\\BootstrapCleaner',
                'Boctulus\\Simplerest\\CodeTools\\Commands\\make\\MakeConsoleCommand',
            ],
        ],
    ];

    foreach ($candidates as $candidate) {
        $candidateConsumer = $temporaryDirectory . DIRECTORY_SEPARATOR . 'consumer-' . str_replace('/', '-', substr($candidate['name'], strlen('boctulus/')));
        if (!mkdir($candidateConsumer, 0700)) {
            throw new RuntimeException("Cannot create independent candidate consumer: {$candidateConsumer}");
        }

        writeConsumerManifest($candidateConsumer, [
            $candidate['name'] => $repositories[$candidate['name']],
        ], [
            'boctulus/simplerest' => '^2.0',
            $candidate['name'] => 'dev-main',
        ], true, ['phpunit/phpunit' => '^10.5']);
        installVcsConsumer($candidateConsumer, true, true);
        runAutoloadSmoke(
            $candidateConsumer,
            array_merge(['Boctulus\\Simplerest\\Core\\Request'], $candidate['classes']),
            [],
            $candidate['name'] === 'boctulus/simplerest-code-tools'
        );

        $packageDirectory = $candidateConsumer . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $candidate['name']);
        $phpunitConfiguration = $packageDirectory . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        if (!is_file($phpunitConfiguration)) {
            throw new RuntimeException("Independent package test configuration is missing: {$phpunitConfiguration}");
        }
        runExecutable([
            PHP_BINARY,
            $candidateConsumer . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpunit',
            '--configuration',
            $phpunitConfiguration,
            '--do-not-cache-result',
        ], $candidateConsumer);

        $lockFile = $candidateConsumer . DIRECTORY_SEPARATOR . 'composer.lock';
        $lock = json_decode((string) file_get_contents($lockFile), true, 512, JSON_THROW_ON_ERROR);
        $stableCore = null;
        foreach (array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []) as $lockedPackage) {
            if (($lockedPackage['name'] ?? null) === 'boctulus/simplerest') {
                $stableCore = $lockedPackage['version'];
                break;
            }
        }
        if ($stableCore === null || str_contains($stableCore, 'dev')) {
            throw new RuntimeException("Candidate consumer did not resolve a stable Packagist core release: " . ($stableCore ?? 'missing'));
        }
        fwrite(STDOUT, "PASS {$candidate['name']} VCS consumer tests against Packagist core {$stableCore}\n");
    }

    fwrite(STDOUT, "PASS independent Composer VCS install, optimized autoload, and package-owned tests for PDF and Code Tools\n");
}

if (in_array('--self-test', $argv, true)) {
    assertDetectorSelfTest();
    exit(0);
}

if (!class_exists(ZipArchive::class)) {
    fwrite(STDERR, "The PHP zip extension is required to inspect Composer archives.\n");
    exit(2);
}

$temporaryDirectory = createTemporaryDirectory();
$artifactDirectory = $temporaryDirectory . DIRECTORY_SEPARATOR . 'artifacts';
$starterDirectory = $temporaryDirectory . DIRECTORY_SEPARATOR . 'starter';
$consumerDirectory = $temporaryDirectory . DIRECTORY_SEPARATOR . 'consumer';
mkdir($artifactDirectory);
mkdir($consumerDirectory);
$exitCode = 0;

try {
    $artifacts = [];
    $packageManifests = glob($root . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'boctulus' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'composer.json') ?: [];
    $archiveJobs = [[
        'label' => 'boctulus/simplerest (Composer release)',
        'directory' => $root,
        'filename' => 'boctulus-simplerest-working-tree',
        'ignoreFilters' => false,
    ]];

    foreach ($packageManifests as $manifest) {
        $packageDirectory = dirname($manifest);
        $package = json_decode((string) file_get_contents($manifest), true, 512, JSON_THROW_ON_ERROR);
        if (empty($package['name'])) {
            throw new RuntimeException("Composer package name missing in {$manifest}");
        }

        $archiveJobs[] = [
            'label' => $package['name'] . ' (Composer release)',
            'directory' => $packageDirectory,
            'filename' => str_replace('/', '-', $package['name']) . '-working-tree',
            'ignoreFilters' => true,
        ];
    }

    $composerManifests = array_merge([$root . DIRECTORY_SEPARATOR . 'composer.json'], $packageManifests);
    foreach ($composerManifests as $manifest) {
        $manifestDirectory = dirname($manifest);
        $manifestName = json_decode((string) file_get_contents($manifest), true, 512, JSON_THROW_ON_ERROR)['name'] ?? basename($manifestDirectory);
        runCommand(['validate', '--strict', '--no-check-publish'], $manifestDirectory);
        fwrite(STDOUT, "PASS Composer manifest validation: {$manifestName}\n");
    }

    foreach ($archiveJobs as $job) {
        $arguments = [
            'archive',
            '--format=zip',
            '--dir=' . $artifactDirectory,
            '--file=' . $job['filename'],
            '--no-scripts',
            '--no-plugins',
            '--no-interaction',
        ];
        if ($job['ignoreFilters']) {
            $arguments[] = '--ignore-filters';
        }

        runCommand($arguments, $job['directory']);
        $archivePath = $artifactDirectory . DIRECTORY_SEPARATOR . $job['filename'] . '.zip';
        if (!is_file($archivePath)) {
            throw new RuntimeException("Composer did not create the expected artifact: {$archivePath}");
        }
        $artifacts[$job['label']] = scanArchive($archivePath, $job['label']);
    }

    $starterMetrics = verifyStarter($root, $starterDirectory);
    if ($withConsumerInstall) {
        $vcsRoot = $temporaryDirectory . DIRECTORY_SEPARATOR . 'vcs';
        mkdir($vcsRoot);
        $repositories = [];
        foreach ($archiveJobs as $job) {
            $package = json_decode((string) file_get_contents($job['directory'] . DIRECTORY_SEPARATOR . 'composer.json'), true, 512, JSON_THROW_ON_ERROR);
            $branchAlias = match ($package['name']) {
                'boctulus/simplerest' => '2.0.x-dev',
                'boctulus/openfactura-sdk' => '1.0.x-dev',
                default => null,
            };
            $repositoryDirectory = $vcsRoot . DIRECTORY_SEPARATOR . str_replace('/', '-', $package['name']);
            $repositories[$package['name']] = createLocalVcsRepository(
                $artifactDirectory . DIRECTORY_SEPARATOR . $job['filename'] . '.zip',
                $repositoryDirectory,
                $branchAlias
            );
        }
        installExternalVcsConsumers($packageManifests, $repositories, $consumerDirectory, $temporaryDirectory);
    }

    foreach ($artifacts as $label => $metrics) {
        fwrite(STDOUT, sprintf("PASS %-52s %4d files %8d zip bytes %8d expanded bytes\n", $label, $metrics['files'], $metrics['bytes'], $metrics['expandedBytes']));
    }
    fwrite(STDOUT, sprintf("PASS %-52s %4d files %8d bytes\n", 'generated application starter', $starterMetrics['files'], $starterMetrics['bytes']));
    if ($withConsumerInstall) {
        fwrite(STDOUT, "PASS external temporary Composer VCS install for all local packages\n");
        fwrite(STDOUT, "PASS independent Composer VCS install for PDF and Code Tools\n");
    }
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . "\n");
    $exitCode = 1;
} finally {
    removeTemporaryDirectory($temporaryDirectory);
}

if ($exitCode !== 0) {
    exit($exitCode);
}

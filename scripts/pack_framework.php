<?php

/**
 * SimpleRest Framework Packager
 * 
 * This script creates a clean distribution copy of the SimpleRest framework
 * from the development directory to a destination directory, preparing it 
 * for distribution via GitHub/composer while removing development-specific configurations.
 * 
 * @author Pablo Bozzolo (boctulus)
 */

use Boctulus\Simplerest\Core\Libs\Files;

class SimpleRestPackager 
{
    private string $sourceDir;
    private string $destDir;
    
    // Base directories/files to exclude (merged with .cpignore if exists)
    private array $ignorePatterns = [];

    // Include patterns that create exceptions to .cpignore (from .cpinclude)
    private array $includePatterns = [];

    // Hardcoded exclusions that are ALWAYS applied regardless of .cpignore
    private array $defaultExclusions = [
        '.git',
        'vendor',
        'node_modules',
        'test-results',
        '.phpunit.cache',
        '*.zip',
        '*.rar',
        '*.tar',
        '*.tar.gz',
        'app/Commands/PackCommand.php',  // PackCommand is only for development
    ];

    public function __construct(string $sourceDir, string $destDir) 
    {
        $this->sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);
        $this->destDir = rtrim($destDir, DIRECTORY_SEPARATOR);
        $this->loadIgnorePatterns();
    }

    /**
     * Load ignore patterns from .cpignore and default exclusions
     */
    private function loadIgnorePatterns(): void
    {
        $this->ignorePatterns = $this->defaultExclusions;

        $cpIgnoreFile = $this->sourceDir . DIRECTORY_SEPARATOR . '.cpignore';
        if (file_exists($cpIgnoreFile)) {
            $lines = file($cpIgnoreFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                $this->ignorePatterns[] = $line;
            }
            echo "Loaded " . count($lines) . " patterns from .cpignore\n";
        }

        // Load include patterns from .cpinclude that create exceptions to .cpignore
        $cpIncludeFile = $this->sourceDir . DIRECTORY_SEPARATOR . '.cpinclude';
        $this->includePatterns = []; // Initialize the include patterns array
        if (file_exists($cpIncludeFile)) {
            $lines = file($cpIncludeFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }
                $this->includePatterns[] = $line;
            }
            echo "Loaded " . count($lines) . " patterns from .cpinclude\n";
        }
    }

    /**
     * Main execution method
     */
    public function run(bool $skipVerification = false, bool $skipComposerInstall = false): bool
    {
        try {
            echo "Starting SimpleRest framework packaging...\n";

            // Clean destination directory first
            $this->cleanDestination();

            // Validate source directory
            if (!$this->validateSource()) {
                throw new Exception("Source directory validation failed");
            }

            // Create destination directory structure
            $this->createDestinationStructure();

            // Copy all top-level files and directories (respecting ignores)
            $this->copyRootContents();

            // Copy FreshCopy templates (overwrites original files)
            $this->copyFreshCopyTemplates();

            // Process composer.json
            $this->processComposerJson();

            // Process config/middlewares.php
            $this->processMiddlewaresConfig();

            // Process config/config.php
            $this->processConfigFile();

            // Run composer install in destination
            if (!$skipComposerInstall) {
                $this->runComposerInstall();
            } else {
                echo "Skipping 'composer install' as requested\n";
            }

            // Copy boot scripts that are required by app.php
            $this->copyBootScripts();

            echo "SimpleRest framework packaging completed successfully!\n";

            if (!$skipVerification) {
                return $this->verify();
            }

            return true;

        } catch (Exception $e) {
            echo "Error during packaging: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Copy all root contents while respecting ignore patterns
     */
    private function copyRootContents(): void
    {
        $items = scandir($this->sourceDir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!$this->shouldInclude($item, $item)) {
                echo "Skipping ignored root item: $item\n";
                continue;
            }

            $sourcePath = $this->sourceDir . DIRECTORY_SEPARATOR . $item;
            $destPath = $this->destDir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($sourcePath)) {
                $this->copyRecursive($sourcePath, $destPath, $item);
                echo "Copied directory: $item\n";
            } else {
                // Skip Windows reserved filenames that can't be copied
                $windowsReservedNames = ['CON', 'PRN', 'AUX', 'NUL',
                                         'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
                                         'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'];

                if (in_array(strtoupper($item), $windowsReservedNames)) {
                    echo "Skipping Windows reserved filename: $item\n";
                    continue;
                }

                if ($this->copyFileWithFixes($sourcePath, $destPath)) {
                    echo "Copied file: $item\n";
                }
            }
        }

        // Explicitly copy the 'com' CLI command file
        $comSourcePath = $this->sourceDir . DIRECTORY_SEPARATOR . 'com';
        $comDestPath = $this->destDir . DIRECTORY_SEPARATOR . 'com';
        if (file_exists($comSourcePath)) {
            if (!Files::cp($comSourcePath, $comDestPath, false, true)) {
                throw new Exception("Failed to copy 'com' file: $comSourcePath to $comDestPath");
            }
            echo "Copied 'com' file\n";
        }
    }

    /**
     * Copy file with potential namespace casing fixes if it's a PHP file
     */
    private function copyFileWithFixes(string $sourcePath, string $destPath): bool
    {
        if (pathinfo($sourcePath, PATHINFO_EXTENSION) === 'php') {
            $this->copyAndFixNamespaceCasing($sourcePath, $destPath);
        } else {
            // Use Files::cp() as suggested
            if (!Files::cp($sourcePath, $destPath, false, true)) {
                throw new Exception("Failed to copy file: $sourcePath to $destPath");
            }
        }
        return true;
    }

    /**
     * Clean the destination directory before packaging
     */
    private function cleanDestination(): void
    {
        if (is_dir($this->destDir)) {
            $this->deleteDirectoryContents($this->destDir);
            echo "Cleaned destination directory: {$this->destDir}\n";
        } else {
            echo "Destination directory does not exist, will be created: {$this->destDir}\n";
        }
    }

    /**
     * Recursively delete directory contents
     */
    private function deleteDirectoryContents(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
    }

    /**
     * Validate that source directory exists and is readable
     */
    private function validateSource(): bool
    {
        if (!is_dir($this->sourceDir)) {
            throw new Exception("Source directory does not exist: {$this->sourceDir}");
        }

        if (!is_readable($this->sourceDir)) {
            throw new Exception("Source directory is not readable: {$this->sourceDir}");
        }

        echo "Source directory validated: {$this->sourceDir}\n";
        return true;
    }

    /**
     * Create the required destination directory structure
     */
    private function createDestinationStructure(): void
    {
        $dirsToCreate = [
            $this->destDir,
            $this->destDir . DIRECTORY_SEPARATOR . 'config',
            $this->destDir . DIRECTORY_SEPARATOR . 'database',
            $this->destDir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
            $this->destDir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeders',
            $this->destDir . DIRECTORY_SEPARATOR . 'logs',
            $this->destDir . DIRECTORY_SEPARATOR . 'public',
            $this->destDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets',
            $this->destDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css',
            $this->destDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img',
            $this->destDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js',
            $this->destDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'fonts',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init' . DIRECTORY_SEPARATOR . 'boot',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init' . DIRECTORY_SEPARATOR . 'redirection',
            $this->destDir . DIRECTORY_SEPARATOR . 'storage',
            $this->destDir . DIRECTORY_SEPARATOR . 'third_party',
            $this->destDir . DIRECTORY_SEPARATOR . 'etc',
            $this->destDir . DIRECTORY_SEPARATOR . 'app',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Modules',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Api',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Background',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Background' . DIRECTORY_SEPARATOR . 'Cronjobs',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Background' . DIRECTORY_SEPARATOR . 'Tasks',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'DAO',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'DTO',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Exceptions',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Helpers',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Interfaces',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Libs',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Middlewares',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Transformers',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Schemas',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Traits',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Locale',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Widgets',
            $this->destDir . DIRECTORY_SEPARATOR . 'vendor',
        ];

        foreach ($dirsToCreate as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new Exception("Failed to create directory: $dir");
                }
                echo "Created directory: $dir\n";
            }
        }
    }

    /**
     * Run composer install in destination directory
     */
    private function runComposerInstall(): void
    {
        echo "Running 'composer install' in destination...\n";

        $originalCwd = getcwd();
        chdir($this->destDir);

        // Remove composer.lock to avoid conflicts with cleaned composer.json
        $lockFile = 'composer.lock';
        if (file_exists($lockFile)) {
            unlink($lockFile);
            echo "Removed composer.lock to regenerate with clean composer.json\n";
        }

        // Check if composer is available
        passthru('composer --version > nul 2>&1', $returnCode);
        if ($returnCode !== 0) {
            echo "Warning: 'composer' command not found. Please install composer manually in destination.\n";
            chdir($originalCwd);
            return;
        }

        passthru('composer install --no-interaction --quiet', $returnCode);

        if ($returnCode !== 0) {
            echo "Warning: 'composer install' failed in destination.\n";
        } else {
            echo "✓ 'composer install' completed.\n";
        }

        chdir($originalCwd);
    }

    /**
     * Copy required boot scripts to destination
     */
    private function copyBootScripts(): void
    {
        $scriptsToCopy = [
            'scripts/init/boot/boot.php',
            'scripts/init/redirection/redirection.php'
        ];

        foreach ($scriptsToCopy as $script) {
            $sourcePath = $this->sourceDir . DIRECTORY_SEPARATOR . $script;
            $destPath = $this->destDir . DIRECTORY_SEPARATOR . $script;

            // Create destination directory if it doesn't exist
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                if (!mkdir($destDir, 0755, true)) {
                    throw new Exception("Failed to create directory: $destDir");
                }
                echo "Created directory: $destDir\n";
            }

            if (file_exists($sourcePath)) {
                if (!Files::cp($sourcePath, $destPath, false, true)) {
                    throw new Exception("Failed to copy boot script: $sourcePath to $destPath");
                }
                echo "Copied boot script: $script\n";
            } else {
                echo "Warning: Boot script does not exist: $sourcePath\n";
            }
        }
    }

    /**
     * Copy test-required files temporarily
     */
    private function copyTestDependencies(): void
    {
        echo "Copying test dependencies temporarily...\n";

        // 1. Create tests/bootstrap.php
        $bootstrapContent = '<?php

        /**
         * PHPUnit Bootstrap File
         *
         * This file is loaded before any tests run.
         * It ensures that a database connection is established for tests that rely on DB::driver().
         */

        require_once __DIR__ . \'/../vendor/autoload.php\';
        require_once __DIR__ . \'/../app.php\';

        use Boctulus\Simplerest\Core\Libs\DB;

        // Establish default database connection
        // This prevents "No db driver" errors in tests that call DB::driver() before DB::table()
        try {
            DB::getConnection();
        } catch (Exception $e) {
            // Connection may fail if DB doesn\'t exist, but driver info is still available
            // This is expected for unit tests that don\'t actually connect to a database
        }
        ';

        $bootstrapPath = $this->destDir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php';
        if (file_put_contents($bootstrapPath, $bootstrapContent) === false) {
            throw new Exception("Failed to create tests/bootstrap.php");
        }
        echo "  Created tests/bootstrap.php\n";

        // 2. Update phpunit.xml to use the bootstrap
        $phpunitXmlPath = $this->destDir . DIRECTORY_SEPARATOR . 'phpunit.xml';
        if (file_exists($phpunitXmlPath)) {
            $phpunitContent = file_get_contents($phpunitXmlPath);
            $phpunitContent = str_replace('bootstrap="vendor/autoload.php"', 'bootstrap="tests/bootstrap.php"', $phpunitContent);
            if (file_put_contents($phpunitXmlPath, $phpunitContent) === false) {
                throw new Exception("Failed to update phpunit.xml");
            }
            echo "  Updated phpunit.xml\n";
        }

        // 3. Backup and modify composer.json to add app/ autoloading
        $composerJsonPath = $this->destDir . DIRECTORY_SEPARATOR . 'composer.json';
        $composerJsonBackupPath = $this->destDir . DIRECTORY_SEPARATOR . 'composer.json.testbackup';

        if (file_exists($composerJsonPath)) {
            copy($composerJsonPath, $composerJsonBackupPath);

            $composerData = json_decode(file_get_contents($composerJsonPath), true);

            // Add app/Controllers, app/Models, and app/Schemas to autoload
            if (!isset($composerData['autoload']['psr-4']['Boctulus\\Simplerest\\Controllers\\'])) {
                $newAutoload = [
                    'Boctulus\\Simplerest\\Core\\' => $composerData['autoload']['psr-4']['Boctulus\\Simplerest\\Core\\'],
                    'Boctulus\\Simplerest\\Controllers\\' => 'app/Controllers/',
                    'Boctulus\\Simplerest\\Models\\' => 'app/Models/',
                    'Boctulus\\Simplerest\\Schemas\\' => 'app/Schemas/',
                ];

                // Add the rest of the autoload entries
                foreach ($composerData['autoload']['psr-4'] as $namespace => $path) {
                    if ($namespace !== 'Boctulus\\Simplerest\\Core\\') {
                        $newAutoload[$namespace] = $path;
                    }
                }

                $composerData['autoload']['psr-4'] = $newAutoload;
            }

            $composerContent = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if (file_put_contents($composerJsonPath, $composerContent) === false) {
                throw new Exception("Failed to update composer.json for tests");
            }
            echo "  Modified composer.json to include app/ autoloading\n";
        }

        // 4. Copy required model and schema files
        $filesToCopy = [
            'app/Models/MyModel.php',
            'app/Models/main/ProductsModel.php',
            'app/Models/main/UsersModel.php',
            'app/Schemas/main/ProductsSchema.php',
            'app/Schemas/main/UsersSchema.php',
        ];

        foreach ($filesToCopy as $file) {
            $sourcePath = $this->sourceDir . DIRECTORY_SEPARATOR . $file;
            $destPath = $this->destDir . DIRECTORY_SEPARATOR . $file;

            if (file_exists($sourcePath)) {
                // Create destination directory if needed
                $destDir = dirname($destPath);
                if (!is_dir($destDir)) {
                    if (!mkdir($destDir, 0755, true)) {
                        throw new Exception("Failed to create directory: $destDir");
                    }
                }

                if (!Files::cp($sourcePath, $destPath, false, true)) {
                    throw new Exception("Failed to copy test dependency: $sourcePath to $destPath");
                }
                echo "  Copied: $file\n";
            }
        }

        // 5. Regenerate autoload
        echo "  Regenerating composer autoload...\n";
        $originalCwd = getcwd();
        chdir($this->destDir);
        exec('composer dump-autoload --quiet 2>&1', $output, $returnCode);
        chdir($originalCwd);

        if ($returnCode !== 0) {
            echo "  Warning: composer dump-autoload returned code $returnCode\n";
        } else {
            echo "  ✓ Autoload regenerated\n";
        }
    }

    /**
     * Remove test-required files after verification
     */
    private function removeTestDependencies(): void
    {
        echo "Removing test dependencies...\n";

        // 1. Restore original composer.json
        $composerJsonPath = $this->destDir . DIRECTORY_SEPARATOR . 'composer.json';
        $composerJsonBackupPath = $this->destDir . DIRECTORY_SEPARATOR . 'composer.json.testbackup';

        if (file_exists($composerJsonBackupPath)) {
            copy($composerJsonBackupPath, $composerJsonPath);
            unlink($composerJsonBackupPath);
            echo "  Restored original composer.json\n";

            // Regenerate autoload with original composer.json
            $originalCwd = getcwd();
            chdir($this->destDir);
            exec('composer dump-autoload --quiet 2>&1', $output, $returnCode);
            chdir($originalCwd);

            if ($returnCode === 0) {
                echo "  ✓ Autoload regenerated with original composer.json\n";
            }
        }

        // 2. Remove copied model and schema files
        $filesToRemove = [
            'app/Models/MyModel.php',
            'app/Models/main/ProductsModel.php',
            'app/Models/main/UsersModel.php',
            'app/Schemas/main/ProductsSchema.php',
            'app/Schemas/main/UsersSchema.php',
        ];

        foreach ($filesToRemove as $file) {
            $filePath = $this->destDir . DIRECTORY_SEPARATOR . $file;

            if (file_exists($filePath)) {
                unlink($filePath);
                echo "  Removed: $file\n";
            }
        }

        // 3. Remove empty directories
        $dirsToCheck = [
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'main',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Schemas' . DIRECTORY_SEPARATOR . 'main',
            $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Schemas',
        ];

        foreach ($dirsToCheck as $dir) {
            if (is_dir($dir) && count(scandir($dir)) === 2) { // Only . and ..
                rmdir($dir);
                echo "  Removed empty directory: " . basename($dir) . "\n";
            }
        }

        // 4. Remove tests/bootstrap.php
        $bootstrapPath = $this->destDir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php';
        if (file_exists($bootstrapPath)) {
            unlink($bootstrapPath);
            echo "  Removed tests/bootstrap.php\n";
        }

        // 5. Restore original phpunit.xml
        $phpunitXmlPath = $this->destDir . DIRECTORY_SEPARATOR . 'phpunit.xml';
        if (file_exists($phpunitXmlPath)) {
            $phpunitContent = file_get_contents($phpunitXmlPath);
            $phpunitContent = str_replace('bootstrap="tests/bootstrap.php"', 'bootstrap="vendor/autoload.php"', $phpunitContent);
            if (file_put_contents($phpunitXmlPath, $phpunitContent) === false) {
                echo "  Warning: Failed to restore phpunit.xml\n";
            } else {
                echo "  Restored phpunit.xml\n";
            }
        }
    }

    /**
     * Verification logic in the destination directory
     */
    public function verify(): bool
    {
        echo "\n--- STARTING VERIFICATION ---\n";

        $originalCwd = getcwd();
        chdir($this->destDir);

        try {
            // Copy test dependencies
            $this->copyTestDependencies();

            // 1. Check php com help
            echo "Testing 'php com help'...\n";
            $output = [];
            exec('php com help 2>&1', $output, $returnCode);
            if ($returnCode !== 0) {
                throw new Exception("'php com help' failed with exit code $returnCode\nOutput: " . implode("\n", $output));
            }
            echo "✓ 'php com help' passed.\n";

            // 2. Check php runalltests.php
            if (file_exists('runalltests.php')) {
                echo "Testing 'php runalltests.php'...\n";
                $output = [];
                exec('php runalltests.php 2>&1', $output, $returnCode);
                $outputStr = implode("\n", $output);
                if (strpos($outputStr, "All tests passed!") === false) {
                     throw new Exception("'php runalltests.php' did not report success.\nOutput: " . $outputStr);
                }
                echo "✓ 'php runalltests.php' passed.\n";
            } else {
                echo "! runalltests.php not found in destination. Skipping.\n";
            }

            // 3. Health check via curl (reading APP_URL from .env if it exists)
            $appUrl = 'http://simplerest.lan'; // fallback
            if (file_exists('.env')) {
                $envContent = file_get_contents('.env');
                if (preg_match('/APP_URL=(.*)/', $envContent, $matches)) {
                    $appUrl = trim($matches[1]);
                }
            }

            echo "Testing API health check at $appUrl...\n";
            $ctx = stream_context_create(['http' => ['timeout' => 5]]);
            $response = @file_get_contents($appUrl, false, $ctx);

            if ($response === false) {
                echo "Warning: Could not reach $appUrl. Ensure the server is running if health check is required.\n";
            } else {
                if (stripos($response, 'Error') !== false || stripos($response, 'Exception') !== false) {
                    throw new Exception("API health check failed! Response contained 'Error' or 'Exception'.");
                }
                echo "✓ API health check passed.\n";
            }

            // Remove test dependencies
            $this->removeTestDependencies();

            // Process .env.example AFTER tests
            chdir($originalCwd);
            $this->processEnvExample();
            chdir($this->destDir);

            echo "--- VERIFICATION COMPLETED SUCCESSFULLY ---\n";
            chdir($originalCwd);
            return true;

        } catch (Exception $e) {
            echo "VERIFICATION FAILED: " . $e->getMessage() . "\n";

            // Try to clean up even on failure
            try {
                $this->removeTestDependencies();
            } catch (Exception $cleanupEx) {
                echo "Warning: Could not clean up test dependencies: " . $cleanupEx->getMessage() . "\n";
            }

            chdir($originalCwd);
            return false;
        }
    }

    /**
     * Copy FreshCopy templates to destination (overwrites original files)
     */
    private function copyFreshCopyTemplates(): void
    {
        $freshCopyDir = $this->sourceDir . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'FreshCopy';

        if (!is_dir($freshCopyDir)) {
            echo "FreshCopy templates directory not found, skipping\n";
            return;
        }

        echo "Copying FreshCopy templates (will overwrite original files)...\n";
        $this->copyFreshCopyRecursive($freshCopyDir, $this->destDir, '');
        echo "✓ FreshCopy templates copied\n";
    }

    /**
     * Copy FreshCopy templates recursively
     */
    private function copyFreshCopyRecursive(string $source, string $dest, string $relativePath): void
    {
        if (!is_dir($source)) {
            return;
        }

        $items = scandir($source);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
            $relativeItemPath = empty($relativePath) ? $item : $relativePath . DIRECTORY_SEPARATOR . $item;
            $destPath = $dest . DIRECTORY_SEPARATOR . $relativeItemPath;

            if (is_dir($sourcePath)) {
                // Create directory if it doesn't exist
                if (!is_dir($destPath)) {
                    if (!mkdir($destPath, 0755, true)) {
                        throw new Exception("Failed to create directory: $destPath");
                    }
                }
                // Recurse into subdirectory
                $this->copyFreshCopyRecursive($sourcePath, $dest, $relativeItemPath);
            } elseif (is_file($sourcePath)) {
                // Copy file (overwrite if exists)
                if (!copy($sourcePath, $destPath)) {
                    throw new Exception("Failed to copy FreshCopy template: $sourcePath to $destPath");
                }
                echo "  Copied FreshCopy template: $relativeItemPath\n";
            }
        }
    }

    /**
     * Process composer.json to remove development-specific configurations
     */
    private function processComposerJson(): void 
    {
        $composerFile = $this->destDir . DIRECTORY_SEPARATOR . 'composer.json';
        
        if (!file_exists($composerFile)) {
            echo "Warning: composer.json does not exist in destination\n";
            return;
        }
        
        $content = file_get_contents($composerFile);
        $composerData = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in composer.json: " . json_last_error_msg());
        }
        
        // Remove development-specific repositories
        if (isset($composerData['repositories'])) {
            $filteredRepositories = [];
            foreach ($composerData['repositories'] as $repo) {
                // Only keep non-path repositories
                if (!isset($repo['type']) || $repo['type'] !== 'path') {
                    $filteredRepositories[] = $repo;
                }
            }
            $composerData['repositories'] = $filteredRepositories;
        }
        
        // Remove development-specific autoload-dev entries
        if (isset($composerData['autoload-dev'])) {
            unset($composerData['autoload-dev']);
        }

        // Remove package references from autoload IF they are not in the destination
        // Only keep mappings that point to included directories (src/, app/ or packages/)
        if (isset($composerData['autoload']['psr-4'])) {
            $filteredAutoload = [];
            foreach ($composerData['autoload']['psr-4'] as $namespace => $path) {
                // Keep core and packages mappings
                if (strpos($path, 'src/') === 0 || strpos($path, 'app/') === 0 || strpos($path, 'packages/') === 0) {
                    $filteredAutoload[$namespace] = $path;
                }
            }
            $composerData['autoload']['psr-4'] = $filteredAutoload;
        }

        // Remove scripts section (dev-specific)
        if (isset($composerData['scripts'])) {
            unset($composerData['scripts']);
        }

        // Define only the minimal required dependencies
        $minimalRequire = [
            'php' => '>=7.4,<8.4',
            'vlucas/phpdotenv' => '^5.2',
        ];

        // Process the require section - only keep minimal dependencies
        $composerData['require'] = $minimalRequire;
        echo "Cleaned require section to only minimal dependencies\n";

        // Clean up require-dev - only keep essential dev dependencies
        if (isset($composerData['require-dev'])) {
            $essentialDev = [];
            if (isset($composerData['require-dev']['phpunit/phpunit'])) {
                $essentialDev['phpunit/phpunit'] = $composerData['require-dev']['phpunit/phpunit'];
            }
            if (isset($composerData['require-dev']['phpstan/phpstan'])) {
                $essentialDev['phpstan/phpstan'] = $composerData['require-dev']['phpstan/phpstan'];
            }

            if (!empty($essentialDev)) {
                $composerData['require-dev'] = $essentialDev;
                echo "Cleaned require-dev section to only essential dev dependencies\n";
            } else {
                unset($composerData['require-dev']); // Remove if empty
            }
        }

        // Add app/Controllers and app/Commands to autoload
        if (isset($composerData['autoload']['psr-4'])) {
            $newAutoload = [];

            // Add Core first
            if (isset($composerData['autoload']['psr-4']['Boctulus\\Simplerest\\Core\\'])) {
                $newAutoload['Boctulus\\Simplerest\\Core\\'] = $composerData['autoload']['psr-4']['Boctulus\\Simplerest\\Core\\'];
            }

            // Add Controllers and Commands autoload
            $newAutoload['Boctulus\\Simplerest\\Controllers\\'] = 'app/Controllers/';
            $newAutoload['Boctulus\\Simplerest\\Commands\\'] = 'app/Commands/';

            // Add the rest
            foreach ($composerData['autoload']['psr-4'] as $namespace => $path) {
                if ($namespace !== 'Boctulus\\Simplerest\\Core\\') {
                    $newAutoload[$namespace] = $path;
                }
            }

            $composerData['autoload']['psr-4'] = $newAutoload;
            echo "Added app/Controllers and app/Commands to autoload\n";
        }

        // Also remove problematic entries from preferred-install config
        if (isset($composerData['config']['preferred-install'])) {
            $filteredPreferredInstall = [];
            $problematicDeps = [
                'boctulus/shopifyconnector',
                'boctulus/dummyapi',
                'boctulus/api-client',
            ];

            foreach ($composerData['config']['preferred-install'] as $dep => $installType) {
                if (!in_array($dep, $problematicDeps)) {
                    $filteredPreferredInstall[$dep] = $installType;
                } else {
                    echo "Removed problematic preferred-install entry: $dep\n";
                }
            }

            // If no valid entries remain, remove the preferred-install config entirely
            if (empty($filteredPreferredInstall)) {
                unset($composerData['config']['preferred-install']);
            } else {
                $composerData['config']['preferred-install'] = $filteredPreferredInstall;
            }
        }

        // Remove extra section if it only contains google api services
        if (isset($composerData['extra'])) {
            // If extra section only has google services, remove it
            if (isset($composerData['extra']['google/apiclient-services']) && count($composerData['extra']) === 1) {
                unset($composerData['extra']);
            }
        }

        // Write the cleaned composer.json back
        $cleanContent = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($composerFile, $cleanContent) === false) {
            throw new Exception("Failed to write processed composer.json");
        }
        
        echo "Processed composer.json to remove development-specific configurations\n";
    }

    /**
     * Process .env.example to sanitize sensitive information
     */
    private function processEnvExample(): void
    {
        $envFile = $this->destDir . DIRECTORY_SEPARATOR . '.env.example';

        if (!file_exists($envFile)) {
            echo "Warning: .env.example does not exist in destination\n";
            return;
        }

        $content = file_get_contents($envFile);

        // Replace database credentials with placeholders (including numbered and named variants)
        $content = preg_replace('/DB_DATABASE(_.+)?=.*/', 'DB_DATABASE$1=', $content);
        $content = preg_replace('/DB_HOST(_.+)?=.*/', 'DB_HOST$1=', $content);
        $content = preg_replace('/DB_PORT(_.+)?=.*/', 'DB_PORT$1=', $content);
        $content = preg_replace('/DB_USERNAME(_.+)?=.*/', 'DB_USERNAME$1=', $content);
        $content = preg_replace('/DB_PASSWORD(_.+)?=.*/', 'DB_PASSWORD$1=', $content);
        $content = preg_replace('/DB_CONNECTION(_.+)?=.*/', 'DB_CONNECTION$1=', $content);

        // Replace email credentials with placeholders (including numbered and named variants)
        $content = preg_replace('/MAIL_(DRIVER|HOST|PORT|USERNAME|PASSWORD|AUTH|ENCRYPTION|DEFAULT_FROM_ADDR|DEFAULT_FROM_NAME)(_.+)?=.*/', 'MAIL_$1$2=', $content);

        // Replace token secret keys with placeholders
        $content = preg_replace('/TOKENS_ACCSS_SECRET_KEY=.*/', 'TOKENS_ACCSS_SECRET_KEY=', $content);
        $content = preg_replace('/TOKENS_REFSH_SECRET_KEY=.*/', 'TOKENS_REFSH_SECRET_KEY=', $content);
        $content = preg_replace('/TOKENS_EMAIL_SECRET_KEY=.*/', 'TOKENS_EMAIL_SECRET_KEY=', $content);

        // Replace OAuth credentials with placeholders
        $content = preg_replace('/OAUTH_GOOGLE_CLIENT_ID=.*/', 'OAUTH_GOOGLE_CLIENT_ID=', $content);
        $content = preg_replace('/OAUTH_GOOGLE_CLIENT_SECRET=.*/', 'OAUTH_GOOGLE_CLIENT_SECRET=', $content);
        $content = preg_replace('/OAUTH_FACEBOOK_CLIENT_ID=.*/', 'OAUTH_FACEBOOK_CLIENT_ID=', $content);
        $content = preg_replace('/OAUTH_FACEBOOK_CLIENT_SECRET=.*/', 'OAUTH_FACEBOOK_CLIENT_SECRET=', $content);

        // Replace Redis credentials with placeholders (including numbered and named variants)
        $content = preg_replace('/REDIS_(HOST|PASSWORD|PORT)(_.+)?=.*/', 'REDIS_$1$2=', $content);

        // Write the sanitized content back
        if (file_put_contents($envFile, $content) === false) {
            throw new Exception("Failed to write processed .env.example");
        }

        echo "Processed .env.example to sanitize sensitive information\n";
    }

    /**
     * Process config/middlewares.php to set default clean content
     */
    private function processMiddlewaresConfig(): void
    {
        $middlewaresFile = $this->destDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'middlewares.php';

        $defaultMiddlewaresContent = '<?php

        /*
            Middleware registration
        */

        return [
            /*
                Examples
            */
            // \'Boctulus\Simplerest\Controllers\TestController\' => InjectGreeting::class
        ];
        ';

        // Write the default middlewares content to the destination
        if (file_put_contents($middlewaresFile, $defaultMiddlewaresContent) === false) {
            throw new Exception("Failed to create config/middlewares.php at: $middlewaresFile");
        }

        echo "Created config/middlewares.php with default content\n";
    }

    /**
     * Process config/config.php to clean ServiceProviders list
     */
    private function processConfigFile(): void
    {
        $configFile = $this->destDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        if (!file_exists($configFile)) {
            echo "Warning: config/config.php does not exist in destination\n";
            return;
        }

        $content = file_get_contents($configFile);

        // Clean the providers section to an empty array
        $content = preg_replace(
            '/\'providers\'\s*=>\s*\[[^\]]*\],/s',
            "'providers' => [\n\t\t// Add your service providers here\n\t\t// Example: Boctulus\\YourPackage\\ServiceProvider::class,\n\t],",
            $content
        );

        // Write the cleaned content back
        if (file_put_contents($configFile, $content) === false) {
            throw new Exception("Failed to write processed config/config.php");
        }

        echo "Processed config/config.php to clean ServiceProviders list\n";
    }

    /**
     * Copy directory recursively, excluding items specified in ignore patterns
     */
    private function copyRecursive(string $source, string $dest, string $relativeRoot = ''): void
    {
        echo "Entering directory: $source (relative: $relativeRoot)\n";
        
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($dest)) {
            if (!mkdir($dest, 0755, true)) {
                throw new Exception("Failed to create directory: $dest");
            }
        }

        $items = scandir($source);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $relativePath = (empty($relativeRoot) ? '' : $relativeRoot . '/') . $item;
            
            if (!$this->shouldInclude($item, $relativePath)) {
                echo "Skipping ignored item: $relativePath\n";
                continue;
            }

            $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
            $destPath = $dest . DIRECTORY_SEPARATOR . $item;

            if (is_dir($sourcePath)) {
                $this->copyRecursive($sourcePath, $destPath, $relativePath);
            } elseif (is_file($sourcePath)) {
                $this->copyFileWithFixes($sourcePath, $destPath);
            }
        }
    }

    /**
     * Determine if an item (file or directory) should be included
     */
    private function shouldInclude(string $filename, string $relativePath): bool
    {
        // First, check hardcoded exclusions that should NEVER be copied
        foreach ($this->defaultExclusions as $excluded) {
            if ($filename === $excluded || $relativePath === $excluded || fnmatch($excluded, $relativePath)) {
                echo "Item '$relativePath' matched default exclusion '$excluded'\n";
                return false;
            }
        }

        // Then, check if the item is explicitly included in .cpinclude (exceptions to .cpignore)
        foreach ($this->includePatterns as $pattern) {
            $pattern = rtrim($pattern, '/');

            // Exact match against relative path
            if ($pattern === $relativePath) {
                echo "Item '$relativePath' matched include pattern '$pattern' exactly (exception to ignore)\n";
                return true;
            }

            // Match against filename
            if (fnmatch($pattern, $filename)) {
                echo "Item '$filename' matched include pattern '$pattern' by filename (exception to ignore)\n";
                return true;
            }

            // Match if item is within an included directory
            if (strpos($relativePath, $pattern . '/') === 0) {
                echo "Item '$relativePath' is within included pattern '$pattern' (exception to ignore)\n";
                return true;
            }

            // Match against relative path with wildcards
            if (fnmatch($pattern, $relativePath) || fnmatch($pattern . '/*', $relativePath)) {
                echo "Item '$relativePath' matched include pattern '$pattern' by relative path (exception to ignore)\n";
                return true;
            }
        }

        // Check if this directory is a parent of any included patterns
        // If so, we need to allow entry to check children
        foreach ($this->includePatterns as $pattern) {
            $pattern = rtrim($pattern, '/');

            // If pattern starts with relativePath, this directory contains included items
            if (strpos($pattern, $relativePath . '/') === 0) {
                echo "Item '$relativePath' is parent of included pattern '$pattern', allowing entry\n";
                return true;
            }
        }

        // Then, check if the item should be ignored
        foreach ($this->ignorePatterns as $pattern) {
            $pattern = rtrim($pattern, '/');

            // Match against filename
            if (fnmatch($pattern, $filename)) {
                echo "Item '$filename' matched ignore pattern '$pattern' by filename\n";
                return false;
            }

            // Match against relative path
            if (fnmatch($pattern, $relativePath) || fnmatch($pattern . '/*', $relativePath)) {
                echo "Item '$relativePath' matched ignore pattern '$pattern' by relative path\n";
                return false;
            }
        }

        return true;
    }

    /**
     * Copy PHP file and fix namespace casing issues for PSR-4 compliance
     */
    private function copyAndFixNamespaceCasing(string $sourcePath, string $destPath): void
    {
        $content = file_get_contents($sourcePath);

        if ($content === false) {
            throw new Exception("Failed to read file: $sourcePath");
        }

        // Fix common namespace casing issues
        $fixedContent = $this->fixNamespaceCasing($content, $sourcePath);

        if (file_put_contents($destPath, $fixedContent) === false) {
            throw new Exception("Failed to write file: $destPath");
        }
    }

    /**
     * Fix namespace casing issues in PHP file content
     */
    private function fixNamespaceCasing(string $content, string $filePath): string
    {
        // Map of known problematic namespaces and their correct casing
        $corrections = [
            // Fix lowercase exceptions to proper case
            // 'Boctulus\Simplerest\Core\Exceptions\\' => 'Boctulus\Simplerest\Core\Exceptions\\',
            // 'Boctulus\Simplerest\Core\Interfaces\\' => 'Boctulus\Simplerest\Core\Interfaces\\',
            // 'Boctulus\Simplerest\Core\Libs\\' => 'Boctulus\Simplerest\Core\Libs\\',
            // 'Boctulus\Simplerest\Core\Traits\\' => 'Boctulus\Simplerest\Core\Traits\\',
            // // More specific corrections
            // 'Boctulus\Simplerest\Core\Interfaces\IObserver' => 'Boctulus\Simplerest\Core\Interfaces\IObserver',
            // 'Boctulus\Simplerest\Core\Interfaces\ISubject' => 'Boctulus\Simplerest\Core\Interfaces\ISubject',
            // 'Boctulus\Simplerest\Core\Libs\EventBus' => 'Boctulus\Simplerest\Core\Libs\EventBus',
            // 'Boctulus\Simplerest\Core\Libs\Fragment' => 'Boctulus\Simplerest\Core\Libs\Fragment',
            // 'Boctulus\Simplerest\SW\core\libs\SSE' => 'Boctulus\Simplerest\Core\Libs\SSE',
            // 'Boctulus\Simplerest\Core\Libs\Strings' => 'Boctulus\Simplerest\Core\Libs\Strings',
            // 'Boctulus\Simplerest\Core\Libs\ViewModel' => 'Boctulus\Simplerest\Core\Libs\ViewModel',
            // 'Boctulus\Simplerest\Core\Traits\EventBusTrait' => 'Boctulus\Simplerest\Core\Traits\EventBusTrait',
        ];

        // Apply corrections
        foreach ($corrections as $incorrect => $correct) {
            // Replace in namespace declarations
            $content = preg_replace('/namespace\s+' . preg_quote($incorrect, '/') . '/', 'namespace ' . $correct, $content);
            // Replace in class declarations
            $content = str_replace($incorrect, $correct, $content);
        }

        return $content;
    }

}

// Main execution
if (php_sapi_name() === 'cli') {
    $sourceDir = ROOT_PATH;
    $destDir   = Files::getAbsolutePath(ROOT_PATH . '../simplerest-pack');
    
    // Allow command-line arguments to override defaults
    if (isset($argv[1]) && isset($argv[2])) {
        $sourceDir = $argv[1];
        $destDir = $argv[2];
    }

    parse_str(implode('&', $_SERVER['argv']), $_GET);    
    $skipComposerInstall =  isset($_GET['--skip-composer-install']) && $_GET['--skip-composer-install'] != 'false';

    $packager = new SimpleRestPackager($sourceDir, $destDir);
    
    if ($packager->run(false, $skipComposerInstall)) {
        exit(0); // Success
    } else {
        exit(1); // Error
    }
}
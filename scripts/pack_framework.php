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
    }

    /**
     * Main execution method
     */
    public function run(bool $skipVerification = false): bool
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

            // Process composer.json
            $this->processComposerJson();

            // Process .env.example
            $this->processEnvExample();

            // Run composer install in destination
            $this->runComposerInstall();

            // Copy boot scripts that are required by app.php
            $this->copyBootScripts();

            // Update routes.php with required content and copy HomeController.php
            $this->updateRoutesAndCopyHomeController();

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
            $this->destDir . DIRECTORY_SEPARATOR . 'database',
            $this->destDir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
            $this->destDir . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeders',
            $this->destDir . DIRECTORY_SEPARATOR . 'config',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init' . DIRECTORY_SEPARATOR . 'boot',
            $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init' . DIRECTORY_SEPARATOR . 'redirection',
            $this->destDir . DIRECTORY_SEPARATOR . 'etc',
            $this->destDir . DIRECTORY_SEPARATOR . 'app',
            $this->destDir . DIRECTORY_SEPARATOR . 'logs',
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
     * Update routes.php with required content and copy HomeController.php
     */
    private function updateRoutesAndCopyHomeController(): void
    {
        // Update routes.php with required content
        $routesDestPath = $this->destDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';

        $requiredRoutesContent = '<?php

use Boctulus\Simplerest\Core\Libs\SiteMap;
use Boctulus\Simplerest\Core\WebRouter;

$route = WebRouter::getInstance();

// Example
WebRouter::any(\'health\', function () {
    return [\'ok\' => true];
});';

        // Write the required routes content to the destination
        if (file_put_contents($routesDestPath, $requiredRoutesContent) === false) {
            throw new Exception("Failed to update routes.php at: $routesDestPath");
        }
        echo "Updated routes.php with required content\n";

        // Copy HomeController.php to prevent 404 errors
        $sourceControllerPath = $this->sourceDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'HomeController.php';
        $destControllerPath = $this->destDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'HomeController.php';

        // Create destination directory if it doesn't exist
        $destDir = dirname($destControllerPath);
        if (!is_dir($destDir)) {
            if (!mkdir($destDir, 0755, true)) {
                throw new Exception("Failed to create directory: $destDir");
            }
            echo "Created directory: $destDir\n";
        }

        if (file_exists($sourceControllerPath)) {
            if (!Files::cp($sourceControllerPath, $destControllerPath, false, true)) {
                throw new Exception("Failed to copy HomeController.php: $sourceControllerPath to $destControllerPath");
            }
            echo "Copied HomeController.php\n";
        } else {
            echo "Warning: HomeController.php does not exist: $sourceControllerPath\n";
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

            echo "--- VERIFICATION COMPLETED SUCCESSFULLY ---\n";
            chdir($originalCwd);
            return true;

        } catch (Exception $e) {
            echo "VERIFICATION FAILED: " . $e->getMessage() . "\n";
            chdir($originalCwd);
            return false;
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
        foreach ($this->ignorePatterns as $pattern) {
            $pattern = rtrim($pattern, '/');
            
            // Match against filename
            if (fnmatch($pattern, $filename)) {
                echo "Item '$filename' matched pattern '$pattern' by filename\n";
                return false;
            }
            
            // Match against relative path
            if (fnmatch($pattern, $relativePath) || fnmatch($pattern . '/*', $relativePath)) {
                echo "Item '$relativePath' matched pattern '$pattern' by relative path\n";
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
    $sourceDir = 'D:\\laragon\\www\\simplerest';
    $destDir = 'D:\\laragon\\www\\simplerest-pack';
    
    // Allow command-line arguments to override defaults
    if (isset($argv[1]) && isset($argv[2])) {
        $sourceDir = $argv[1];
        $destDir = $argv[2];
    }
    
    $packager = new SimpleRestPackager($sourceDir, $destDir);
    
    if ($packager->run()) {
        exit(0); // Success
    } else {
        exit(1); // Error
    }
}
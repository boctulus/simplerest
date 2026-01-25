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

class SimpleRestPackager 
{
    private string $sourceDir;
    private string $destDir;
    
    // Directories to exclude from copying
    private array $excludedDirs = [
        '.git',
        'vendor',
        'node_modules',
        'backups',
        'docker',
        'docs',
        'examples',
        'exports',
        'test-results',
        'tests',
        'webautomation',
        'yakpro-po',
        'storage',
        '__releases',
        '.agent',
        '.claude',
        '.codegpt',
        '.phpunit.cache',
        'prompts',
        'third_party',
        'packages',  // We'll handle packages differently if needed
    ];
    
    // Files to exclude from copying
    private array $excludedFiles = [
        'composer.lock',  // Will be regenerated
        'debug.log',      // Contains debug info
        'phpunit.xml',    // Dev config
        'phpunit_output.txt',  // Dev artifact
        'test_output.txt',     // Dev artifact
        'test_output2.txt',    // Dev artifact
        'test_results.txt',    // Dev artifact
        'output.xml',          // Dev artifact
        'runtest',             // Dev script
        'push',                // Dev script
        'winpush.ps1',         // Dev script
        'update_version.ps1',  // Dev script
        'zipcore.ps1',         // Dev script
        'zippackages.ps1',     // Dev script
        'ziptests.ps1',        // Dev script
        'zipthis.ps1',         // Dev script
        'fix_model_references.php',  // Dev script
        'fix_namespace.php',         // Dev script
        'fix_producto_marcas.php',   // Dev script
        'verify_controller_setup.php',  // Dev script
        'verify_routing_issue.php',     // Dev script
        'debug_routes.php',             // Dev script
        'test_*.php',                  // All test files
        'test_*.bat',                  // All test batch files
        'test_*.sh',                   // All test shell scripts
        'test_*.ps1',                  // All test PowerShell scripts
        'test_*.txt',                  // All test text files
        'test_*.xml',                  // All test XML files
        'debug_*.php',                 // All debug files
        'debug_*.bat',                 // All debug batch files
        'debug_*.sh',                  // All debug shell files
        'debug_*.ps1',                 // All debug PowerShell files
        'debug_*.txt',                 // All debug text files
        'debug_*.xml',                 // All debug XML files
    ];

    public function __construct(string $sourceDir, string $destDir) 
    {
        $this->sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);
        $this->destDir = rtrim($destDir, DIRECTORY_SEPARATOR);
    }

    /**
     * Main execution method
     */
    public function run(): bool
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

            // Copy src directory
            $this->copySrcDirectory();

            // Copy app directory (first level only)
            $this->copyAppDirectory();

            // Copy config directory
            $this->copyConfigDirectory();

            // Copy essential root files
            $this->copyEssentialFiles();

            // Copy scripts/init directory
            $this->copyScriptsInitDirectory();

            // Process composer.json
            $this->processComposerJson();

            // Process .env.example
            $this->processEnvExample();

            echo "SimpleRest framework packaging completed successfully!\n";
            return true;

        } catch (Exception $e) {
            echo "Error during packaging: " . $e->getMessage() . "\n";
            return false;
        }
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
            $this->destDir . DIRECTORY_SEPARATOR . 'etc',
            $this->destDir . DIRECTORY_SEPARATOR . 'app',
            $this->destDir . DIRECTORY_SEPARATOR . 'logs',
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
     * Copy the entire src directory recursively
     */
    private function copySrcDirectory(): void 
    {
        $source = $this->sourceDir . DIRECTORY_SEPARATOR . 'src';
        $dest = $this->destDir . DIRECTORY_SEPARATOR . 'src';
        
        if (is_dir($source)) {
            $this->copyRecursive($source, $dest);
            echo "Copied src directory\n";
        } else {
            echo "Warning: Source src directory does not exist: $source\n";
        }
    }

    /**
     * Copy first-level contents of the app directory
     */
    private function copyAppDirectory(): void 
    {
        $source = $this->sourceDir . DIRECTORY_SEPARATOR . 'app';
        $dest = $this->destDir . DIRECTORY_SEPARATOR . 'app';
        
        if (is_dir($source)) {
            $items = scandir($source);
            
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                
                $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
                $destPath = $dest . DIRECTORY_SEPARATOR . $item;
                
                if (is_dir($sourcePath)) {
                    $this->copyRecursive($sourcePath, $destPath);
                    echo "Copied app/{$item} directory\n";
                }
            }
        } else {
            echo "Warning: Source app directory does not exist: $source\n";
        }
    }

    /**
     * Copy the entire config directory
     */
    private function copyConfigDirectory(): void 
    {
        $source = $this->sourceDir . DIRECTORY_SEPARATOR . 'config';
        $dest = $this->destDir . DIRECTORY_SEPARATOR . 'config';
        
        if (is_dir($source)) {
            $this->copyRecursive($source, $dest);
            echo "Copied config directory\n";
        } else {
            echo "Warning: Source config directory does not exist: $source\n";
        }
    }

    /**
     * Copy essential root files
     */
    private function copyEssentialFiles(): void
    {
        $essentialFiles = [
            'README.md',
            'LICENSE',
            'index.php',
            'app.php',
            '.htaccess',
            '.gitignore',
            'CHANGELOG.txt',
            '.env.example',
            'composer.json',
        ];

        foreach ($essentialFiles as $file) {
            $sourceFile = $this->sourceDir . DIRECTORY_SEPARATOR . $file;
            $destFile = $this->destDir . DIRECTORY_SEPARATOR . $file;

            if (file_exists($sourceFile) && $this->shouldIncludeFile($file)) {
                if (!copy($sourceFile, $destFile)) {
                    throw new Exception("Failed to copy file: $sourceFile");
                }
                echo "Copied file: $file\n";
            } else {
                if (!file_exists($sourceFile)) {
                    echo "Warning: Essential file does not exist: $file\n";
                }
            }
        }
    }

    /**
     * Copy scripts/init directory
     */
    private function copyScriptsInitDirectory(): void
    {
        $source = $this->sourceDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init';
        $dest = $this->destDir . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'init';

        if (is_dir($source)) {
            $this->copyRecursive($source, $dest);
            echo "Copied scripts/init directory\n";
        } else {
            echo "Warning: Source scripts/init directory does not exist: $source\n";
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

        // Remove package references from autoload since packages are excluded from distribution
        // Only keep core framework autoload mappings (src/ and app/)
        if (isset($composerData['autoload']['psr-4'])) {
            $filteredAutoload = [];
            foreach ($composerData['autoload']['psr-4'] as $namespace => $path) {
                // Only keep mappings that point to included directories (src/ or app/)
                if ($path === 'src/' || strpos($path, 'app/') === 0) {
                    $filteredAutoload[$namespace] = $path;
                }
            }
            $composerData['autoload']['psr-4'] = $filteredAutoload;
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
     * Copy directory recursively, excluding specified directories and files
     */
    private function copyRecursive(string $source, string $dest): void
    {
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($dest)) {
            if (!mkdir($dest, 0755, true)) {
                throw new Exception("Failed to create directory: $dest");
            }
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($source) + 1);
            $destPath = $dest . DIRECTORY_SEPARATOR . $relativePath;

            // Check if the item should be excluded
            $itemName = basename($item->getPathname());
            $itemDir = dirname($item->getPathname());

            // Check if parent directory should be excluded
            $shouldExclude = false;
            $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
            foreach ($pathParts as $part) {
                if (in_array($part, $this->excludedDirs)) {
                    $shouldExclude = true;
                    break;
                }
            }

            if ($shouldExclude) {
                continue;
            }

            if ($item->isDir()) {
                if (!is_dir($destPath)) {
                    if (!mkdir($destPath, 0755, true)) {
                        throw new Exception("Failed to create directory: $destPath");
                    }
                }
            } elseif ($item->isFile()) {
                if ($this->shouldIncludeFile($itemName)) {
                    if (pathinfo($item->getPathname(), PATHINFO_EXTENSION) === 'php') {
                        // Fix PSR-4 compliance issues by correcting namespace casing
                        $this->copyAndFixNamespaceCasing($item->getPathname(), $destPath);
                    } else {
                        if (!copy($item->getPathname(), $destPath)) {
                            throw new Exception("Failed to copy file: {$item->getPathname()} to $destPath");
                        }
                    }
                }
            }
        }
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

    /**
     * Determine if a file should be included in the copy
     */
    private function shouldIncludeFile(string $filename): bool 
    {
        foreach ($this->excludedFiles as $pattern) {
            if ($this->matchPattern($pattern, $filename)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if filename matches the given pattern (supports wildcards)
     */
    private function matchPattern(string $pattern, string $filename): bool 
    {
        if (strpos($pattern, '*') !== false) {
            $regex = str_replace('\*', '.*', preg_quote($pattern, '/'));
            return preg_match("/^$regex$/", $filename);
        }
        
        return $pattern === $filename;
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
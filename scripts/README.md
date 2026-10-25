# Legacy SimpleRest starter packager

This page documents the legacy starter-application generator in `scripts/pack_framework.php`. It is not the Composer core PACK implementation. The authoritative Composer workflow is `php com pack build --profile=core` and is specified in `docs/architecture/pack-contract.md`.

The legacy generator remains for the starter verification suite. It accepts only a new disposable destination and never refreshes or cleans `D:\laragon\www\simplerest-pack`.

## Purpose

The packager script performs the following tasks:
- Creates a clean distribution of the SimpleRest framework
- Removes development-specific configurations and sensitive information
- Prepares the framework for distribution via GitHub or composer
- Maintains all essential functionality while sanitizing sensitive data

## Features

- Requires the destination not to exist, so existing files are never cleaned
- Recursively copies the `src/` directory
- Copies first-level contents of the `app/` directory
- Copies the `scripts/init` directory with boot and redirection files
- Processes configuration files to remove sensitive information
- Creates necessary directory structure in the destination
- Excludes development artifacts and sensitive files
- Sanitizes `.env.example` and `composer.json` files
- Removes problematic dependencies that reference local packages
- Minimizes composer dependencies to only essential requirements

## Usage

### Command Line Interface

Invoke the legacy workflow explicitly with source and a new disposable destination:

```bash
php scripts/pack_framework.php --legacy-starter /path/to/source /path/to/new-disposable-destination
```

Example:
```bash
php scripts/pack_framework.php --legacy-starter /home/user/my-project /tmp/my-project-starter
```

## What Gets Copied

### Directories Created in Destination
- `database/migrations`
- `database/seeders`
- `config`
- `scripts`
- `etc`
- `app` (with all first-level subdirectories)
- `logs`

### Files Copied
- All files from `src/` directory (recursively)
- All first-level directories from `app/` directory
- Configuration files from `config/` directory
- Essential root files:
  - `composer.json` (sanitized)
  - `README.md`
  - `LICENSE`
  - `index.php`
  - `app.php`
  - `.htaccess`
  - `.gitignore`
  - `CHANGELOG.txt`
  - `.env.example` (sanitized)

## What Gets Excluded

The following directories and files are excluded from the copy:
- `.git/` directory
- `vendor/` directory
- `node_modules/` directory
- `logs/` directory contents
- `storage/` directory contents
- `backups/` directory
- `docker/` directory
- `docs/` directory
- `examples/` directory
- `exports/` directory
- `test-results/` directory
- `tests/` directory
- `webautomation/` directory
- `yakpro-po/` directory
- Any temporary or debug files (e.g., `test_*.php`, `debug*.php`, etc.)
- `composer.lock` file
- Debug logs and test output files

## Configuration Sanitization

### .env.example Processing
The script sanitizes the `.env.example` file by replacing actual credentials with placeholders:
- Database connection details
- Email credentials
- OAuth credentials
- Token secret keys
- Redis password

### composer.json Processing
The script cleans the `composer.json` file by:
- Removing path repositories pointing to local packages
- Removing development-specific repositories
- Removing development-specific autoload-dev entries
- Preserving all necessary dependencies

## Security Measures

- Ensures no sensitive information is copied (passwords, tokens, API keys, etc.)
- Replaces any hardcoded credentials with placeholders
- Sanitizes configuration files to remove personal/development information

## Requirements

- PHP 8.1 or higher (same as SimpleRest framework requirements)
- Sufficient disk space for the destination directory
- Proper file system permissions to read source and write destination

## Exit Codes

- `0`: Success - Packaging completed successfully
- `1`: Error - An error occurred during packaging

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**: Ensure you have read permissions for the source directory and write permissions for the destination directory.

2. **Disk Space Issues**: Ensure sufficient disk space is available for the destination directory.

3. **Missing Source Directory**: Verify that the source directory exists and is accessible.

4. **Invalid JSON in composer.json**: If the script fails due to invalid JSON, check the composer.json file for syntax errors.

## Notes

- The destination directory will be created if it doesn't exist
- The destination must not already exist; existing files are never overwritten
- The script preserves the directory structure of the source
- After packaging, run `composer install` in the destination directory to install dependencies

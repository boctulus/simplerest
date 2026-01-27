# SimpleRest Framework Packager

This script creates a clean distribution copy of the SimpleRest framework from the development environment to a destination directory, preparing it for distribution via GitHub/composer while removing development-specific configurations.

## Purpose

The packager script performs the following tasks:
- Creates a clean distribution of the SimpleRest framework
- Removes development-specific configurations and sensitive information
- Prepares the framework for distribution via GitHub or composer
- Maintains all essential functionality while sanitizing sensitive data

## Features

- Cleans the destination directory before packaging to remove any previous artifacts
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

Run the script from the command line:

```bash
php scripts/pack_framework.php
```

By default, the script will:
- Use `D:\laragon\www\simplerest` as the source directory
- Use `D:\laragon\www\simplerest-pack` as the destination directory

### Custom Directories

You can specify custom source and destination directories:

```bash
php scripts/pack_framework.php /path/to/source /path/to/destination
```

Example:
```bash
php scripts/pack_framework.php /home/user/my-project /home/user/my-project-dist
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

- PHP 7.4 or higher (same as SimpleRest framework requirements)
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
- Existing files in the destination directory will be overwritten
- The script preserves the directory structure of the source
- After packaging, run `composer install` in the destination directory to install dependencies
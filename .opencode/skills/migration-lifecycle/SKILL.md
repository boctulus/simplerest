---
name: migration-lifecycle
description: Complete guide for creating, running, rolling back, and managing migrations in SimpleRest, including module/package contexts.
---

# Migration Lifecycle Skill

SimpleRest has **15 migration commands** under the `migrations` group.

## Quick Reference

```bash
php com migrations help              # list all migration commands
php com migrations create --help     # help for a specific command
php com migrate                      # shorthand for "migrations migrate"
```

## Step 1: Create a Migration

### Standard (app-level)

```bash
php com make migration create_products_table
```

This generates a file in `database/migrations/` with timestamp prefix.

### For a Module

```bash
php com make migrations:module ModuleName create_products_table --create
```

### For a Package

```bash
php com make migrations:package vendor/package-name create_products_table --create
```

### Migration File Structure

```php
<?php

use Boctulus\Simplerest\Libs\DB;

function up() {
    DB::statement("CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        cost DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL
    )");
}

function down() {
    DB::statement("DROP TABLE IF EXISTS products");
}
```

> [!IMPORTANT]
> Use `DB::statement()` not raw PDO. The framework automatically applies table prefixes.

## Step 2: Run Migrations

```bash
# Run all pending
php com migrate

# Run for a specific connection
php com migrations migrate --to=zippy

# Run migrations from a specific directory (e.g., a package)
php com migrate --dir=packages/boctulus/api-client/database/migrations

# Run a specific migration file
php com migrations migrate --file=2026_01_01_000000_create_products_table
```

## Step 3: Rollback

```bash
# Rollback last batch
php com migrations rollback

# Rollback all
php com migrations reset

# Rollback and re-run all
php com migrations refresh

# Rollback and re-run all including seeders
php com migrations fresh --seed
```

## Step 4: Seeders

```bash
php com make seeder ProductsSeeder
php com db:seed                    # run all seeders
php com db:seed --class=ProductsSeeder
```

### Seeder Example

```php
<?php

use Boctulus\Simplerest\Libs\DB;

function run() {
    DB::table('products')->insert([
        ['name' => 'Product A', 'cost' => 100],
        ['name' => 'Product B', 'cost' => 200],
    ]);
}
```

## Step 5: Status & Logs

```bash
php com migrations status              # show pending/completed
php com migrations log                 # migration history
php com migrations log --to=zippy      # for specific connection
```

## Common Patterns

### Creating a Table with Schema Support

```php
function up() {
    DB::statement("CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        cost DECIMAL(10,2) NOT NULL DEFAULT 0,
        belongs_to INT NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        FOREIGN KEY (belongs_to) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_products_name (name),
        INDEX idx_products_belongs_to (belongs_to)
    )");
}
```

After migration, generate the schema:
```bash
php com make schema products
```

### Adding a Column

```php
function up() {
    DB::statement("ALTER TABLE products ADD COLUMN discount DECIMAL(5,2) DEFAULT 0 AFTER cost");
}

function down() {
    DB::statement("ALTER TABLE products DROP COLUMN discount");
}
```

### Adding an Index

```php
function up() {
    DB::statement("CREATE INDEX idx_products_slug ON products(slug)");
}

function down() {
    DB::statement("DROP INDEX idx_products_slug ON products");
}
```

### Package-Specific Migration

```bash
# Create
php com make migrations:package boctulus/api-client create_api_logs_table --create

# Run
php com migrate --dir=packages/boctulus/api-client/database/migrations --to=logs_db

# Or register in package ServiceProvider
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "No table_name defined" | The `migrations` table doesn't exist. Run `php com migrate` once with a valid connection. See `docs/_internal/issues/migration_table_name_error.md` |
| Table prefix not applied | Ensure `tb_prefix` is set in `config/databases.php` for the connection |
| Migration not found | Check `--dir` path is correct and contains `.php` files with `up()` function |
| "Class not found" | Run `composer dump-autoload` |
| Foreign key fails | Ensure referenced table and column exist, or defer FK checks with `SET FOREIGN_KEY_CHECKS=0` |

## Best Practices

1. **One change per migration** — don't combine unrelated schema changes
2. **Always implement `down()`** — enables clean rollbacks
3. **Test rollback** after creating each migration
4. **Use `--to` for multi-tenant** — specify the connection explicitly
5. **Version control** all migration files
6. **Prefix package migrations** with the package name to avoid collisions
7. **Use seeders for reference data** — not migrations
8. **Run `php com make acl --force`** after creating tables that need ACL

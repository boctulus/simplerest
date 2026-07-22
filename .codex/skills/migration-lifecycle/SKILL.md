---
name: migration-lifecycle
description: Complete guide for creating, running, rolling back, and managing migrations in SimpleRest, including module/package contexts.
---

# Migration Lifecycle Skill

15 migration commands under `migrations` group.

## Quick Reference

```bash
php com migrations help                    # list all commands
php com migrate                            # shorthand
php com make migration create_products_table
php com make migrations:module ModuleName create_products_table --create
php com make migrations:package vendor/package create_products_table --create
```

## Migration File

```php
<?php
use Boctulus\Simplerest\Libs\DB;

function up() {
    DB::statement("CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        cost DECIMAL(10,2) NOT NULL,
        belongs_to INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,
        FOREIGN KEY (belongs_to) REFERENCES users(id) ON DELETE CASCADE
    )");
}

function down() {
    DB::statement("DROP TABLE IF EXISTS products");
}
```

> Use `DB::statement()` — framework auto-applies table prefixes.

## Commands

```bash
php com migrate                                        # run all pending
php com migrations migrate --to=zippy                  # specific connection
php com migrate --dir=packages/.../migrations           # specific directory
php com migrations rollback                            # last batch
php com migrations reset                               # all
php com migrations refresh                             # rollback + migrate
php com migrations fresh --seed                        # reset + migrate + seed
php com migrations status                              # pending/completed
php com migrations log                                 # history
```

## Seeders

```bash
php com make seeder ProductsSeeder
php com db:seed                                        # run all
php com db:seed --class=ProductsSeeder                 # specific
```

## After Migration

```bash
php com make schema products                           # generate schema for auto-endpoints
php com make acl --force                               # regenerate ACL if needed
```

## Common Patterns

```php
// Add column
function up() { DB::statement("ALTER TABLE products ADD COLUMN discount DECIMAL(5,2) DEFAULT 0 AFTER cost"); }
function down() { DB::statement("ALTER TABLE products DROP COLUMN discount"); }

// Add index
function up() { DB::statement("CREATE INDEX idx_products_slug ON products(slug)"); }
function down() { DB::statement("DROP INDEX idx_products_slug ON products"); }
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "No table_name defined" | Run `php com migrate` once (creates migrations table) |
| Prefix not applied | Set `tb_prefix` in `config/databases.php` |
| Migration not found | Check `--dir` path contains `.php` files with `up()` |
| Class not found | Run `composer dump-autoload` |
| FK fails | Order migrations correctly or use SET FOREIGN_KEY_CHECKS=0 |

## Best Practices

- One change per migration, always implement `down()`
- Test rollback after each migration
- Use `--to` for multi-tenant connections
- Version control all migration files
- Use seeders for reference data, not migrations

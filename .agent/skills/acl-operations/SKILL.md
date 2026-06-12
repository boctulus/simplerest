---
name: acl-operations
description: Covers the 23 CLI commands for day-to-day ACL management including role assignment, permissions, deny rules, and debugging.
---

# ACL Operations Skill

This skill covers the **operational** side of ACL — using the 23 CLI commands to manage users, roles, permissions, and debugging.

For ACL **configuration** (editing `config/acl.php`), use the `acl-config` skill.

## Quick Start

```bash
php com acl help                        # full command list
php com acl <command> --help            # details for one command
```

## Role Management

```bash
# List all roles
php com acl list-roles
php com acl list-roles --format=json

# Inspect a role's compiled policy
php com acl inspect-role admin
php com acl inspect-role --role=supervisor

# Assign a role to a user
php com acl assign-role --email=user@example.com --role=supervisor

# Remove a role from a user
php com acl remove-role --email=user@example.com --role=supervisor

# Preview without modifying
php com acl assign-role --email=user@example.com --role=supervisor --dry-run

# List all users with their roles
php com acl list-user-roles
php com acl list-user-roles --role=admin       # filtered by role
php com acl list-user-roles --role=null        # users without role

# Show roles for a specific user
php com acl show-user-roles user@example.com
```

## Special Permissions

```bash
# List all available special permissions
php com acl list-sp

# Grant a special permission to a user
php com acl grant-sp --email=user@example.com --perm=impersonate

# Revoke a special permission
php com acl revoke-sp --email=user@example.com --perm=impersonate

# List a user's individual special permissions
php com acl list-user-sp user@example.com
```

Common special permissions: `read_all`, `write_all`, `lock`, `grant`, `impersonate`, `fill_all`.

## Table (Resource) Permissions

```bash
# Grant table permissions
php com acl grant-tb --email=user@example.com --table=products --perm=create
php com acl grant-tb --email=user@example.com --table=products --perm=read

# Revoke table permissions
php com acl revoke-tb --email=user@example.com --table=products --perm=delete

# List user's table permissions
php com acl list-user-tb user@example.com

# Clear ALL table permissions for a user
php com acl clear-tb --email=user@example.com --table=products --dry-run
php com acl clear-tb --email=user@example.com --table=products --force

# Replace full permission set
php com acl replace-tb --email=user@example.com --table=products --perms=show,list,create --dry-run
php com acl replace-tb --email=user@example.com --table=products --perms=show,list,create --force
```

Valid `--perm` values: `show | list | create | update | delete | show_all | list_all | read | write`

> `read` = `show+list`, `write` = `create+update+delete`

## Deny Rules

DENY has highest precedence: **DENY > USER_GRANT > ROLE_GRANT**.

```bash
# Add a deny rule
php com acl add-deny --email=user@example.com --resource=products --action=delete

# Remove a deny rule
php com acl remove-deny --email=user@example.com --resource=products --action=delete

# List deny rules for a user
php com acl list-deny user@example.com
```

## Debugging & Diagnostics

```bash
# Quick check: does user have permission?
php com acl can --email=user@example.com --perm=delete --resource=products
php com acl can --email=user@example.com --perm=impersonate

# Full resolution chain (DENY > USER_GRANT > ROLE_GRANT)
php com acl explain --email=user@example.com --perm=delete --resource=products
php com acl explain --email=user@example.com --perm=impersonate

# Complete effective permissions for a user
php com acl resolve user@example.com
php com acl resolve --email=user@example.com --only=sp
php com acl resolve --email=user@example.com --format=json

# Validate ACL consistency
php com acl validate
```

## Regenerating ACL from Config

After modifying `config/acl.php`:

```bash
php com acl make
php com acl make --force        # overwrite existing
php com acl make --debug        # show generated ACL
```

## Precedence System

```
DENY  >  USER_GRANT  >  ROLE_GRANT
```

| Layer | Table | Description |
|-------|-------|-------------|
| DENY | `user_deny_permissions` | Blocks without exception |
| USER_GRANT | `user_tb_permissions`, `user_sp_permissions` | Direct per-user grants |
| ROLE_GRANT | `user_roles` → `roles` | Inherited from role assignment |

## Database Tables

| Table | Purpose |
|-------|---------|
| `sp_permissions` | Available capabilities catalog |
| `roles` | Registered roles |
| `user_roles` | Role-to-user assignments |
| `user_sp_permissions` | Individual SP grants |
| `user_tb_permissions` | Individual table grants |
| `user_deny_permissions` | Explicit deny rules |

## See Also

- `config/acl.php` — static ACL definition
- `php com acl help` — full CLI reference
- [`docs/commands/AclCommands.md`](../docs/commands/AclCommands.md) — complete command reference
- [`docs/ACL.md`](../docs/ACL.md) — architecture & DSL
- `acl-config` skill — configuring ACL in `config/acl.php`

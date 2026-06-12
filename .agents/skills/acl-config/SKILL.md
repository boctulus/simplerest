---
name: acl-config
description: Guides the configuration of the Access Control List (ACL) in SimpleRest, including adjusting roles, inheritance, and resource permissions in config\acl.php.
---

# ACL Configuration Skill

This skill guides you through adjusting the `config/acl.php` file based on project needs, managing roles, and configuring granular resource permissions.

## Key Concepts

SimpleRest uses a multi-layered permission system:
- **Roles**: Logical groups of permissions (e.g., `guest`, `registered`, `admin`).
- **Inheritance**: Roles can inherit permissions from other roles.
- **Special Permissions**: Global system-wide permissions (e.g., `read_all`, `write_all`, `impersonate`, `lock`).
- **Resource Permissions**: Permissions specific to a table/resource (e.g., `show`, `list`, `create`, `update`, `delete`).

## Step 1: Analyze Requirements

Before making changes, determine:
1. What roles are needed?
2. Which tables/resources need specific access control?
3. Should a role inherit from another?

## Step 2: Modify `config/acl.php`

Open `config/acl.php`. Use the `$acl` object to define roles and permissions.

### Defining Roles
```php
$acl->addRole('vendedor', 10);
```

### Inheriting Roles
> [!IMPORTANT]
> `addInherit()` must be called **before** adding new permissions to the inheriting role.

```php
$acl->addRole('vendedor', 10)
    ->addInherit('guest');
```

### Adding Special Permissions
```php
$acl->addSpecialPermissions(['read_all', 'write_all'], 'admin');
```

### Adding Resource (Table) Permissions
Standard permissions: `show`, `list`, `create`, `update`, `delete`.
Aliases: `read` (`show` + `list`), `write` (`create` + `update` + `delete`).

```php
$acl->addResourcePermissions('products', ['read', 'create'], 'vendedor');
```

To allow access to records owned by others (e.g., for public listings or supervisors):
`show_all`, `list_all`, `read_all`.

```php
$acl->addResourcePermissions('posts', ['read_all'], 'guest');
```

## Step 3: Set Guest and Registered Roles

Ensure the system knows which roles represent guests and registered users:
```php
$acl->setAsGuest('guest');
$acl->setAsRegistered('registered');
```

## Step 4: Regenerate ACL Cache

After modifying `config/acl.php`, you **must** regenerate the ACL cache:

```bash
php com make acl --force
```

To see the resulting ACL structure for debugging:
```bash
php com make acl --force --debug
```

## Step 5: Verification

1. **Check for syntax errors**:
   ```bash
   php -l config/acl.php
   ```
2. **Review output** of the `--debug` command to ensure the roles and permissions are correctly mapped.
3. **Verify via API**: Test an endpoint with a specific role token to ensure permissions are enforced as expected.

## Common Special Permissions

- `read_all`: Access records of other users.
- `write_all`: Modify/Delete records of other users.
- `lock`: Lock/Unlock records or modify locked records.
- `grant`: Conceder roles and permissions.
- `impersonate`: Ability to act as another user.
- `fill_all`: Modify non-fillable fields or creation dates.

## Troubleshooting

- **Inheritance Error**: "You can't inherit permissions from 'X' when you have already permissions for 'Y'".
  - **Solution**: Call `addInherit()` immediately after `addRole()` and before `addResourcePermissions()` or `addSpecialPermissions()`.
- **Changes not reflecting**:
  - **Solution**: Run `php com make acl --force`.
- **401/403 Errors**:
  - Check if the user has the correct role assigned in the `user_roles` table.
  - Verify if a "decorator" permission in `user_tb_permissions` or `user_sp_permissions` is overriding the role.

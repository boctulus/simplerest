---
name: create-endpoint
description: Guides the creation of a new API endpoint in SimpleRest, including migrations, schemas, models, controllers, and ACL.
---

# Creating a New API Endpoint

This skill guides you through the process of creating a complete API endpoint in the SimpleRest framework. It covers the standard locations as well as modules and packages.

## Prerequisites

- Access to the terminal.
- Knowledge of the database connection to use.
- Defined table name and model name.

## Step 1: Create Database Migration

General command:
```bash
php com make migration create_{table_name}_table
```

### Context-Specific Migrations

If working within a **Module**:
```bash
php com make migrations:module {ModuleName} create_{table_name}_table --create
```

If working within a **Package**:
```bash
php com make migrations:package {package_name} create_{table_name}_table --create
```

Open the generated .php file and edit it.

## Step 2: Execute Migration

Run all pending migrations:
```bash
php com migrate
```

## Step 3: Generate Schema

Generate the schema file for the table:
```bash
php com make schema {table_name}
```

> [!NOTE]
> If the table is in a different database connection, use `--from={connection_id}`.

## Step 4: Create Model

Generate the model for the table:
```bash
php com make model {ModelName}
```

> [!TIP]
> This command uses the schema generated in the previous step. If you want to skip schema check, use `--no-check`.

## Step 5: Create API Controller

Generate the API controller:
```bash
php com make api {ControllerName}
```

> [!IMPORTANT]
> The controller will be created in `app/Controllers/Api/` by default. If you need it in a Module or Package, move it manually and adjust the namespace.

## Step 6: Configure ACL (Access Control List)

1. Open `config/acl.php`.
2. Add the resource permissions for your table:

```php
$acl->addResourcePermissions('{table_name}', ['show', 'list', 'create', 'update', 'delete'], '{role_name}');
```

Common roles: `guest`, `registered`, `admin`.

3. Regenerate the ACL cache:
```bash
php com make acl --force
```

## Step 7: Testing the Endpoint

Test the standard CRUD operations:

1. **List records**: `GET /api/v1/{table_name}`
2. **Show record**: `GET /api/v1/{table_name}/{id}`
3. **Create record**: `POST /api/v1/{table_name}`
4. **Update record**: `PUT /api/v1/{table_name}/{id}`
5. **Delete record**: `DELETE /api/v1/{table_name}/{id}`

Use `curl`, ApiClient utility or a tool like Playwright for UI-dependent tests.

## Troubleshooting

- **404 Error**: Ensure the controller exists, extends `MyApiController`, and the table name matches.
- **Permission Error**: Check `config/acl.php` and verify user roles.
- **Database Error**: Ensure the schema file exists in `app/Schemas/` and is correctly configured.

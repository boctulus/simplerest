---
name: schemas
description: Complete guide for creating, editing, and using database schemas in SimpleRest — foundation for auto-endpoints, AutoJoins, validation, and relationships.
---

# Schemas Skill

Schemas are the **foundation** of SimpleRest's auto-generated API. They define table structure, relationships, validation rules, and enable zero-config REST endpoints.

## Location

```
app/Schemas/{connection}/
```

Each database connection has its own directory:

```
app/Schemas/
├── main/             # default connection
├── az/
├── complex01/
└── zippy/
```

## Creating a Schema

### From existing table (recommended):
```bash
php com make schema products
php com make schema products --from=zippy        # different connection
```

### Manual creation:
```php
<?php
// app/Schemas/main/products.php

return [
    'table'  => 'products',
    'columns' => [
        'id' => [
            'type'       => 'integer',
            'primary'    => true,
            'autoincrement' => true,
        ],
        'name' => [
            'type'       => 'string',
            'length'     => 100,
            'nullable'   => false,
            'validation' => ['required', 'max:100'],
        ],
        'price' => [
            'type'       => 'decimal',
            'precision'  => 10,
            'scale'      => 2,
            'nullable'   => false,
        ],
        'category_id' => [
            'type'       => 'integer',
            'nullable'   => true,
            'foreign'    => ['categories', 'id'],
        ],
        'description' => [
            'type'       => 'text',
            'nullable'   => true,
        ],
        'created_at' => [
            'type'       => 'timestamp',
            'nullable'   => true,
        ],
        'updated_at' => [
            'type'       => 'timestamp',
            'nullable'   => true,
        ],
        'deleted_at' => [
            'type'       => 'timestamp',
            'nullable'   => true,  // enables soft delete
        ],
    ],
];
```

## Column Types

| Type | Schema Builder | Description |
|------|---------------|-------------|
| `string` | `$s->string('name', 100)` | VARCHAR |
| `integer` | `$s->integer('id')` | INT |
| `bigint` | `$s->bigint('id')` | BIGINT |
| `decimal` | `$s->decimal('price', 10, 2)` | DECIMAL |
| `float` | `$s->float('amount')` | FLOAT |
| `boolean` | `$s->boolean('active')` | BOOLEAN/TINYINT |
| `text` | `$s->text('body')` | TEXT |
| `mediumtext` | `$s->mediumtext('body')` | MEDIUMTEXT |
| `longtext` | `$s->longtext('body')` | LONGTEXT |
| `json` | `$s->json('metadata')` | JSON |
| `date` | `$s->date('birth')` | DATE |
| `datetime` | `$s->datetime('created')` | DATETIME |
| `timestamp` | `$s->timestamp('created')` | TIMESTAMP |
| `time` | `$s->time('start')` | TIME |
| `binary` | `$s->binary('data')` | BLOB |
| `enum` | `$s->enum('status', ['a','b'])` | ENUM |

## Relationships for AutoJoins

```php
'relationships' => [
    'posts' => [
        ['users.id', 'posts.user_id']         // 1:N
    ],
    'profile' => [
        ['users.id', 'profiles.user_id']      // 1:1
    ],
    'tags' => [
        ['posts.id', 'post_tag.post_id'],      // N:M (pivot)
        ['tags.id', 'post_tag.tag_id']
    ],
],
```

> Auto-joins are automatic — no controller code needed. Just defining the relationship in the schema enables it.

## Expanded Relationships (for SubResources)

```php
'expanded_relationships' => [
    'category' => [
        [['categories', 'id'], ['products', 'category_id']]
    ],
],
```

## Validation Rules in Schemas

Inline validation per field — used by auto-endpoints automatically:

```php
'email' => [
    'type'       => 'string',
    'length'     => 255,
    'validation' => ['required', 'email', 'unique:users'],
],
```

## Schema Builder (Programmatic)

The `Schema` class (`src/framework/Libs/Schema.php`) can create tables from code:

```php
$schema = new Schema('products');
$schema->increments('id');
$schema->string('name', 100);
$schema->integer('category_id')->nullable();
$schema->decimal('price', 10, 2);
$schema->text('description');
$schema->timestamps();              // created_at + updated_at
$schema->softDeletes();             // deleted_at
$schema->foreign('category_id')->references('id')->on('categories');
$schema->create();
```

## Schema + Auto-Endpoints

A schema file alone enables:

| Feature | How |
|---------|-----|
| CRUD endpoints | `/api/v1/products` auto-generated |
| Filtering | `?name=Vodka&cost[gteq]=10` |
| Pagination | `?page=2&pageSize=20` |
| Sorting | `?order[name]=ASC` |
| Field selection | `?fields=id,name,cost` |
| Aggregates | `?props=avg(cost)` |
| AutoJoins | Based on `relationships` |
| SubResources | Based on `expanded_relationships` |
| Validation | Based on `validation` per column |

## Common Tasks

### Add a column to schema:
```php
'discount' => [
    'type'       => 'decimal',
    'precision'  => 5,
    'scale'      => 2,
    'nullable'   => true,
    'default'    => 0,
],
```

### Disable auto-endpoint for a schema:
Remove the schema file or use `--no-check` on model generation.

## See Also

- [`docs/Schemas.md`](../docs/Schemas.md) — full schema reference
- [`AutoJoins.md`](../docs/AutoJoins.md) — automatic joins from schemas
- [`SubResources.md`](../docs/SubResources.md) — nested CRUD from relationships
- [`AuthenticationEndpoints-Summary.md`](../docs/AutomaticEndpoints-Summary.md) — auto-endpoints
- [`SimpleRest-API-Rest.md`](../docs/SimpleRest-API-Rest.md) — REST query syntax
- `create-api-endpoint-guide` skill — full endpoint creation workflow
- `query-builder` skill — using the QB with schema-aware features

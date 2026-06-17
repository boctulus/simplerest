# Schemas — SimpleRest

## ¿Qué es un Schema?

Un **Schema** es una definición estructurada de una tabla de base de datos. SimpleRest usa archivos schema para:

1. **Definir estructura de tablas** (columnas, tipos, constraints)
2. **Generar migraciones** automáticamente
3. **Auto-descubrir relaciones** (foreign keys → AutoJoins)
4. **Generar endpoints REST automáticos**
5. **Definir reglas de validación** por campo
6. **Documentar la base de datos**

---

## Ubicación

```
app/Schemas/{connection}/
```

Cada conexión de BD tiene su propio directorio:

```
app/Schemas/
├── main/             # Conexión principal
├── az/
├── complex01/
├── edu/
├── laravelshopify/
└── zippy/
```

## Estructura de un Schema

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
            'nullable'   => true,  // Soft delete
        ],
    ],
    'relationships' => [
        'category' => [
            ['categories.id', 'products.category_id']
        ],
    ],
    'expanded_relationships' => [
        'category' => [
            [['categories', 'id'], ['products', 'category_id']]
        ],
    ],
];
```

## Tipos de Columna Soportados

| Tipo | Schema Builder | Descripción |
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

## Schema Builder (Clase)

La clase `Schema` en `src/framework/Libs/Schema.php` (2184 líneas) permite construir tablas programáticamente:

```php
$schema = new Schema('products');
$schema->increments('id');
$schema->string('name', 100);
$schema->integer('category_id')->nullable();
$schema->decimal('price', 10, 2);
$schema->text('description');
$schema->timestamps();        // created_at + updated_at
$schema->softDeletes();       // deleted_at
$schema->foreign('category_id')->references('id')->on('categories');
$schema->create();            // Ejecutar CREATE TABLE
```

## Relaciones

Las relaciones se definen en el schema y son usadas por **AutoJoins**:

```php
'relationships' => [
    'posts' => [
        ['users.id', 'posts.user_id']     // 1:N
    ],
    'profile' => [
        ['users.id', 'profiles.user_id']  // 1:1
    ],
    'tags' => [
        ['posts.id', 'post_tag.post_id'],  // N:M (pivot)
        ['tags.id', 'post_tag.tag_id']
    ],
],
```

## Validación desde Schema

Los schemas pueden incluir reglas de validación por campo:

```php
'email' => [
    'type'       => 'string',
    'length'     => 255,
    'validation' => ['required', 'email', 'unique:users'],
],
```

## Generación de Endpoints Automáticos

Si existe un schema para `products`, el endpoint `/api/v1/products` se auto-genera con CRUD completo, filtrado, paginación y ordenamiento.

Ver: [`AutomaticEndpoints-Summary.md`](./AutomaticEndpoints-Summary.md)

## Ver También

- [`QueryBuilder.md`](./QueryBuilder.md) — Query Builder
- [`AutoJoins.md`](./AutoJoins.md) — joins automáticos desde schemas
- [`SubResources.md`](./SubResources.md) — sub-recursos desde relaciones
- [`Validation.md`](./Validation.md) — reglas de validación
- [`SimpleRest-API-Rest.md`](./SimpleRest-API-Rest.md) — REST queries

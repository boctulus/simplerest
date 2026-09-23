# AutoJoins — SimpleRest

> **Audit status:** This legacy page is quarantined. `QueryBuilderTrait::join()` contains a schema-relationship inference branch and `joinTo()` exists, but automatic 1:N/N:M resolution, pivot metadata, join qualification, and the old usage examples are not yet verified. See the [claim-level audit](../../README.md#claim-level-findings-query-builder); automatic joins remain pending the schema audit.

## Descripción

SimpleRest puede inferir **automáticamente las condiciones de JOIN** a partir de las relaciones definidas en los schemas, eliminando la necesidad de especificar manualmente las columnas de relación.

**Estado**: ✅ Completo

---

## Uso

```php
// JOIN automático desde schema
$users = DB::table('users')
    ->join('profiles')
    ->get();

// Equivalente manual (que NO necesitas escribir):
// SELECT * FROM users LEFT JOIN profiles ON users.id = profiles.user_id
```

## Multi-JOIN

```php
$results = DB::table('users')
    ->join(['profiles', 'roles', 'posts'])
    ->where('users.active', 1)
    ->get();
```

## Calificación Automática

Los campos se califican automáticamente para evitar ambigüedad:

```php
// Sin calificar → automático
$users = DB::table('users')
    ->join('profiles')
    ->where('active', 1)     // Se convierte en users.active
    ->get();
```

## Cómo Funciona

Las condiciones de JOIN se extraen de `expanded_relationships` en el schema:

```php
// app/Schemas/main/users.php
'expanded_relationships' => [
    'posts' => [
        [['users', 'id'], ['posts', 'user_id']]
    ],
    'profile' => [
        [['users', 'id'], ['profiles', 'user_id']]
    ],
],
```

## Tipos de JOIN

Por defecto usa `LEFT JOIN`. Se puede especificar:

```php
DB::table('users')
    ->join('profiles', 'INNER')
    ->get();
```

## Relaciones N:M (Pivot)

```php
// Schema con relación many-to-many
'relationships' => [
    'tags' => [
        ['posts.id', 'post_tag.post_id'],
        ['tags.id', 'post_tag.tag_id']
    ],
],

// Uso
DB::table('posts')
    ->join('tags')
    ->get();
```

## Ver También

- [`Schemas.md`](./Schemas.md) — legacy schema material (pending audit)
- [`SubResources.md`](./SubResources.md) — sub-recursos desde relaciones
- [`QueryBuilder.md`](./QueryBuilder.md) — documentación completa del QB

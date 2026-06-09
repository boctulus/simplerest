# ORM — Estado Actual

> **⚠️ Este documento existe para aclarar el estado del ORM en SimpleRest.**

## El ORM No es Funcional

Se intentó implementar un ORM al estilo Laravel/Eloquent (con Active Record, relaciones lazy loading, etc.) pero **no es funcional**. El código existe en el repositorio pero no está operativo ni debe usarse en producción.

## Qué Usar en su Lugar

SimpleRest tiene un **Query Builder poderoso y probado** que trabaja con **arrays planos** (no objetos ORM):

```php
// ✅ Query Builder — la vía correcta
$users = DB::table('users')
    ->where('active', 1)
    ->orderBy('name')
    ->get();

// ✅ Model con Query Builder
$userModel = new User(true);
$users = $userModel->where('role', 'admin')->get();
```

### Ventajas del Query Builder sobre ORM

- **Arrays planos**: menor overhead de memoria, más rápido
- **Soporte multi-DB**: MySQL, PostgreSQL, SQLite, SQL Server, Oracle, etc.
- **AutoJoins**: desde schemas, relaciones inferidas por FK
- **Sub-Resources**: CRUD anidado desde schemas
- **Paginación integrada**: `Paginator` class
- **Transacciones**: `DB::beginTransaction()`, `commit()`, `rollback()`
- **Caché de queries**: `DB::table('x')->cached()->get()`

### Documentación Relacionada

| Documento | Contenido |
|-----------|-----------|
| [`QueryBuilder.md`](./QueryBuilder.md) | Documentación completa del QB |
| [`SimpleRest-API-Rest.md`](./SimpleRest-API-Rest.md) | API REST queries (filter, sort, paginate) |
| [`AutomaticEndpoints-Summary.md`](./AutomaticEndpoints-Summary.md) | Endpoints REST automáticos |

### Futuro

No hay planes inmediatos para resucitar el ORM. El Query Builder cubre todos los casos de uso con mejor performance y simplicidad. Si en el futuro se implementa un ORM, será como capa opcional sobre el QB existente.

---

## Model Base (`src/framework/Model.php`)

El modelo base (`Model`) sí es funcional pero actúa como **wrapper del Query Builder**, no como ORM:

```php
class UserModel extends Model {
    protected $table_name = 'users';
}

$users = (new UserModel(true))->where('active', 1)->get();
```

Devuelve **arrays**, no objetos. Soporta hooks de ciclo de vida: `boot()`, `onReading()`, `onCreating()`, `onCreated()`, etc.

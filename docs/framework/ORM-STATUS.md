# ORM — Estado comprobado

Actualizado: 2026-09-23.

## Arquitectura real

`Model` incorpora `QueryBuilderTrait` y conserva la API existente de consultas. `get()` y `first()` devuelven arrays; `create()`, `update()`, `delete()`, filtros, joins, orden y límites siguen ejecutándose en el Query Builder. `Model::exists()` comprueba si la consulta encuentra filas.

La capa de entidad es `ModelRecord`. `Model::newInstance($attributes, $exists)` crea una entidad; `firstRecord()`, `getRecords()` y `findRecord($id)` convierten resultados del Query Builder en entidades. `ModelRecord::exists()` representa el estado de persistencia de esa entidad. Esta separación evita alterar los significados de `Model::exists()`, `first()` y `get()` usados por el resto del framework.

La dependencia de consultas es `ModelRecord → Model / QueryBuilderTrait → DB / PDO`. La entidad no construye SQL; reutiliza la conexión del modelo o la conexión actual de `DB`. Sus operaciones de escritura crean un Query Builder limpio del mismo modelo y delegan en `create()`, `update()` y `delete()`; las operaciones existentes de schema, fillable, hooks y soft delete quedan en esa capa.

## Flujos comprobados

```php
$record = UserModel::newInstance(['name' => 'Ana']);
$record->save();

$record = (new UserModel(true))->findRecord($record->id);
$record->name = 'Ana María';
$record->save();
$record->delete();

$records = (new UserModel(true))
    ->where(['active' => 1])
    ->orderBy(['name' => 'ASC'])
    ->limit(10)
    ->getRecords();
```

Los tests de SQLite comprueban creación, lectura, hidratación, filtro, orden, límite, actualización de campos modificados, aislamiento por clave primaria, borrado, estado de persistencia y un schema con clave primaria personalizada. `first()` sigue devolviendo un array. La prueba de escritura utiliza las operaciones reales del Query Builder sobre una tabla SQLite temporal.

## Límites y decisiones pendientes

- No hay lazy loading ni API de relaciones entre entidades implementada. El Query Builder ya ofrece `join()`, `joinTo()` y `connectTo()` para consultas relacionadas. La semántica de relaciones de objetos no puede deducirse de la implementación ni de los tests y requiere una decisión explícita antes de añadirse.
- Las llamadas estáticas como `UserModel::where(...)` de la documentación interna antigua no están implementadas. Los métodos homónimos existentes son de instancia; no se cambió ese contrato.
- `ModelRecord::save()` necesita una tabla configurada. Para una entidad persistida también necesita su clave primaria original; modificar esa clave en la entidad no está admitido. Las consultas por lotes y actualizaciones masivas siguen correspondiendo al Query Builder.
- La documentación en `docs/framework/_internal/to-do/orm/` contiene aspiraciones antiguas y ejemplos Laravel-like. No constituye prueba de funcionalidades presentes.

## Validación

- `php vendor/phpunit/phpunit/phpunit unit-tests/query-builder --filter 'SimpleORMTest|DB_TransactionTest'`: 10 tests, 33 assertions, todos correctos.
- `php vendor/phpunit/phpunit/phpunit unit-tests/query-builder`: 65 tests, 192 assertions, 9 errores, 11 fallos, 1 omitido en la ejecución del 2026-09-23. Los errores incluyen PostgreSQL inaccesible, fixtures/columnas ausentes y resolución de modelos de test; los fallos restantes son discrepancias de SQL esperado en tests del Query Builder. No son prueba de los flujos de entidad nuevos ni fueron corregidos en esta etapa.

Para consultas de alto volumen que no necesitan entidades, usar directamente `Model`/`DB::table()` y sus arrays. Véase [QueryBuilder.md](./QueryBuilder.md).

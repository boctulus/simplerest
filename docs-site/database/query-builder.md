---
title: Query Builder
source:
  - docs/database/query-builder.md
  - docs/audit/README.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# Query Builder

Esta referencia describe los caminos actuales del código y los separa de resultados de base de datos ejecutados. No se ejecutó consulta ni test PHPUnit para esta revisión.

## Construcción y ejecución

`DB::table($from, $alias = null, $connect = true)` resuelve un modelo de aplicación para un nombre de tabla; si `$from` contiene ` FROM ` crea `MyModel` y usa `fromRaw()`. El default conecta. `Model` directo usa `$connect = false` por defecto. Ambos caminos usan `QueryBuilderTrait`.

`select()` acepta campos separados, array o lista separada por comas. `where()` acepta formas `(campo, valor)`, `(campo, operador, valor)` o array. `toSql()` compila SQL y bindings pero no ejecuta. En la ruta ordinaria, `get()` prepara, enlaza y ejecuta PDO si `$exec` está activo; con la ejecución desactivada devuelve `false`.

`dontExec()` no garantiza un flujo sin conexión: `DB::table()` conecta por default y `bind()` puede conectar antes de revisar `$exec`. Si el esquema del modelo contiene `deleted_at`, el builder agrega `deleted_at IS NULL` hasta habilitar filas eliminadas.

## Filtros y SQL raw

`where()` y sus variantes construyen predicados; hay helpers `orWhere()`, `whereNull()`, `whereNotNull()`, `whereBetween()`, `whereLike()`, `whereDate()`, `whereExists()` y agrupaciones con closures. El SQL emitido puede depender del esquema y del driver; aquí no se ejecutó.

**No uses `whereIn()` ni `whereNotIn()` con datos no confiables.** Su rama de array concatena valores al SQL y envuelve texto entre comillas sin placeholders ni escape en ese camino. Es un riesgo observado en fuente, no un exploit reproducido. `whereRaw()`, `orWhereRaw()`, `selectRaw()`, `havingRaw()`, `joinRaw()` y `fromRaw()` aceptan fragmentos SQL; nunca interpoles entrada no confiable.

`orHavingRaw()` es un stub: el cuerpo está comentado y el método no devuelve el builder.

## Proyección, joins y límites

La clase ofrece `select()`, `addSelect()`, `selectRaw()`, `distinct()`, joins `join`, `leftJoin`, `rightJoin`, `crossJoin`, `naturalJoin` y `joinRaw`; `joinTo()`/`connectTo()` dependen de metadatos de relaciones. Para ordenar están `orderBy()`, variantes asc/desc, `orderByRaw()` y `reorder()`. `limit()`/`take()` y `offset()`/`skip()` fijan límites. `paginate($page, $page_size)` calcula `(tamaño * página) - tamaño`, toma `paginator.default_limit` o 10 si falta un tamaño, y sólo configura el límite y el offset. No calcula el total de filas ni retorna metadatos de página. Las aserciones de fuente inspeccionadas fijan offset 0 para página 1 y offset 20 para página 3; no se ejecutaron. La clase `Paginator`, los parámetros API y los metadatos de respuesta están en [Paginación](/database/pagination).

`groupBy()`, `having()`, `union()` y `unionAll()` están declarados; su compatibilidad por dialecto no se probó. Para joins inferidos y subrecursos, consulta [Relaciones, joins y subrecursos](/database/relations).

En esta versión, los nombres cronológicos difieren del uso esperado: `oldest()` produce orden descendente por `created_at`, `latest()` delega a `oldest()` y `newest()` produce ascendente. Tests de fuente afirman dos formas SQL; no se ejecutaron.

## Resultados y agregados

- `get()` obtiene las filas; un resultado vacío ejecutado es `[]`, mientras que la ruta sin ejecución devuelve `false`.
- `first()` obtiene una fila y devuelve `[]` cuando no encuentra ninguna. `firstOrFail()` lanza ante resultado vacío; `getOne()` y `top()` delegan a `first()`.
- `find()` añade la condición y devuelve el builder. `findOr()` y `findOrFail()` verifican existencia antes de usar el fallback o lanzar.
- `value()` obtiene un escalar; `pluck()` selecciona una columna; `assoc()`, `asObject()` y `column()` cambian el fetch mode.
- `get(..., $pristine = true)` y `first(..., $pristine = true)` omiten mutators/transformers de salida.
- `count()`, `avg()`, `sum()`, `min()` y `max()` usan `aggregate()`. La ruta sin `groupBy()` castea el escalar a entero, incluso `AVG` y `SUM`, lo que puede perder decimales. La ruta agrupada castea el resultado de `fetchAll()` a entero, así que no ofrece un contrato de colección. Ningún agregado se ejecutó contra una base real.

## Escrituras y borrados

`create()`, `insert()`, `rawInsert()`, `bulkInsert()`, `createOrIgnore()`, `createOrUpdate()`, `insertOrIgnore()` e `insertOrUpdate()` siguen caminos distintos de hooks y transacción. No se ejecutaron efectos de escritura. La [referencia de SQL raw](/database/raw-queries) cubre la fachada estática `DB`; la [referencia de transacciones](/database/transactions) cubre límites y rollback.

`update()` requiere datos no vacíos, conexión y un `WHERE` no vacío; después aplica configuración de mutación, fillables, validación y hooks. `delete()` puede marcar el registro como borrado lógico si el modelo lo configura; `forceDelete()` selecciona borrado físico y exige condición. `truncate()` no tiene filtro por filas.

::: danger `preview()` y `simulate()` no protegen todas las escrituras
Hay controles en algunos caminos de insert, pero `update()` y `delete()` pueden ejecutar cuando `$exec` está activo sin respetar el modo seleccionado. `$exec` y el modo de ejecución son controles distintos. No trates esos modos como garantía general de no escritura.
:::

El scope soft delete depende del esquema y expone `setSoftDelete()`, `deleted()`, `showDeleted()`, `withTrashed()`, `onlyTrashed()`, `trashed()`, `is_trashed()`, `undelete()` y `restore()`.

## Inventario de métodos

El siguiente inventario agrupa los métodos públicos declarados en `QueryBuilderTrait`. Es un índice de nombres a nivel de fuente, no una afirmación de que cada método se haya ejercitado. Los helpers protegidos y privados quedan fuera; `orHavingRaw()` aparece para señalar que es un stub.

| Área | Métodos |
| --- | --- |
| Tabla, conexión, esquema y campos | `getTableAlias`, `getKeyName`, `getTableName`, `setPaginator`, `setValidator`, `setTableAlias`, `alias`, `table`, `setTable`, `prefix`, `setPrefix`, `removePrefix`, `fromRaw`, `setStrictModeHaving`, `dontQualify`, `qualify`, `wrap`, `addPrefix`, `getSchema`, `hasSchema`, `inSchema`, `getIdName`, `id`, `getAttr`, `getFieldNames`, `getformatters`, `getHidden`, `getNotHidden`, `getAutoFields`, `findTableByAlias` |
| Fillability, visibilidad, validación y transforms | `addRules`, `noValidation`, `fill`, `unfill`, `fillAll`, `hide`, `unhide`, `unhideAll`, `isNullable`, `isFillable`, `getFillables`, `getNotFillables`, `setNullables`, `addNullables`, `removeNullables`, `getNullables`, `getNotNullables`, `getUniques`, `getRules`, `getRule`, `getMissing`, `getFieldOrder`, `registerInputMutator`, `registerOutputMutator`, `registerTransformer`, `applyInputMutator`, `applyOutputMutators`, `applyTransformer` |
| Fetch y control | `setFetchMode`, `assoc`, `asObject`, `column`, `dontExec`, `dontBind`, `doBind`, `setExecutionMode`, `getExecutionMode`, `simulate`, `preview`, `normalExecution`, `setSqlFormatter`, `sqlformatterOff`, `sqlformatterOn`, `sqlFormatter` |
| Proyección, predicados y SQL raw | `select`, `addSelect`, `selectRaw`, `where`, `whereOr`, `orWhere`, `orWhereLike`, `whereColumn`, `whereNot`, `whereNull`, `whereNotNull`, `whereIn`, `whereNotIn`, `whereBetween`, `whereNotBetween`, `whereLike`, `whereDate`, `whereExists`, `whereRegEx`, `whereRegExp`, `whereNotRegEx`, `whereNotRegExp`, `whereRaw`, `orWhereRaw`, `firstWhere`, `group`, `and`, `or`, `andNot`, `not`, `orNot`, `when`, `where_array`, `whereFormedQuery`, `having`, `havingRaw`, `havingFormedQuery`, `_having`, `orHaving`, `orHavingRaw` (stub), `getWhere`, `getWhereVals`, `getWhereVars`, `getWhereRawVals`, `getHavingVals`, `getHavingVars`, `getHavingRawVals` |
| Joins, orden y límites | `join`, `joinRaw`, `leftJoin`, `rightJoin`, `crossJoin`, `naturalJoin`, `joinTo`, `connectTo`, `orderBy`, `orderByRaw`, `orderByAsc`, `orderByDesc`, `reorder`, `take`, `limit`, `offset`, `skip`, `paginate`, `groupBy`, `random`, `rand`, `oldest`, `latest`, `newest`, `distinct`, `union`, `unionAll` |
| Ejecución y resultados | `toSql`, `getBindings`, `mergeBindings`, `query`, `get`, `first`, `firstOrFail`, `getOne`, `top`, `find`, `findOr`, `findOrFail`, `value`, `exists`, `pluck`, `count`, `avg`, `sum`, `min`, `max`, `getLastPrecompiledQuery`, `getLastBindingParamters`, `dd`, `getLog`, `debug`, `logSQL`, `getLastOperation`, `getCurrentOperation`, `getOp` |
| Escrituras, borrado y timestamps | `create`, `createOrIgnore`, `createOrUpdate`, `insert`, `rawInsert`, `bulkInsert`, `insertOrIgnore`, `insertOrUpdate`, `getInsertVals`, `update`, `updateOrFail`, `touch`, `delete`, `forceDelete`, `truncate`, `setSoftDelete`, `deleted`, `showDeleted`, `withTrashed`, `onlyTrashed`, `trashed`, `is_trashed`, `undelete`, `restore`, `createdAt`, `createdBy`, `updatedAt`, `updatedBy`, `deletedAt`, `deletedBy`, `isLocked`, `locked`, `belongsTo`, `isDirty` |

Se conserva la grafía actual `getLastBindingParamters()`.

`ModelTest`, `QueryBuilderExtendedTest`, `PGSQLQueryBuilderTest` y `SimpleORMTest` contienen fixtures o aserciones de SQL seleccionados; solo se inspeccionaron. La conexión SQLite en memoria declarada por `SimpleORMTest` no se abrió durante esta revisión. Para conexiones, consulta [Base de datos](/database); para hidratación y persistencia, [Model y ModelRecord](/database/model).

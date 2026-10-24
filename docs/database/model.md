# Modelo y registros

`Model` es el punto de composición del Query Builder y de configuración de una tabla. `ModelRecord` añade una API de entidad mutable por fila; ambos caminos coexisten. Esta página describe el código actual. Los tests citados se inspeccionaron, pero no se ejecutaron durante esta auditoría.

## Qué ofrece `Model`

La clase base [`Model`](../../src/framework/Model.php#L14) usa [`QueryBuilderTrait`](../../src/framework/Traits/QueryBuilderTrait.php#L21), `SubResourceHandler` e `InsertWithSubResourcesTrait`. Por eso un modelo puede construir consultas y delegar lecturas relacionadas; la presencia de esos traits no demuestra que todas las operaciones de relaciones o escrituras anidadas estén completas. El ledger mantiene esas afirmaciones por separado.

El constructor es `__construct(bool $connect = false, $schema = null, bool $load_config = true)`. Si recibe una clase de esquema, llama a su `get()` y toma de ahí el nombre de tabla, los atributos, tipos, reglas y clave. Si `$connect` es verdadero, obtiene la conexión actual. El helper `DB::table()` pasa `$connect = true` por defecto y resuelve un modelo de aplicación para el nombre de tabla; instanciar un modelo directamente usa el default `false`.

La clase permite configurar el PDO con `setConn()` y leerlo con `getConn()`. La guía de [Query Builder](query-builder.md) explica la diferencia entre SQL compilado, conexión y ejecución; [Conexiones](connections.md) describe la selección del handle.

## Modelo de esquema y comportamiento de la instancia

Cuando el esquema está presente, el constructor inicializa los campos desde `attr_types`, aplica las reglas y fillables disponibles y activa soft delete si el esquema contiene el campo configurado `deleted_at`. Las escrituras siguen las reglas del Query Builder; no asumas que todas las variantes (`create`, `insert`, `rawInsert`, `bulkInsert`) llaman los mismos hooks o manejan transacciones igual.

`Model` declara los hooks protegidos `boot()`, `init()`, `onReading()`, `onRead($count)`, `onCreating(&$data)`, `onCreated(&$data, $last_inserted_id)`, `onUpdating(&$data)`, `onUpdated(&$data, $count)`, `onDeleting(&$data)`, `onDeleted(&$data, $count)`, `onRestoring(&$data)` y `onRestored(&$data, $count)`. Son puntos de extensión vacíos en la clase base. El código llama a distintos hooks desde caminos distintos; no constituyen una secuencia universal para toda escritura.

## Consultas que hidratan registros

El Query Builder tradicional conserva sus resultados en el tipo de fetch configurado. Estos métodos adicionales devuelven `ModelRecord`:

| Método | Resultado y origen |
| --- | --- |
| [`newInstance($attributes = [], $exists = false)`](../../src/framework/Model.php#L30) | Crea un registro con los atributos dados; por defecto se considera aún no persistido. |
| [`firstRecord()`](../../src/framework/Model.php#L36) | Clona el modelo de consulta, fuerza fetch asociativo y envuelve la primera fila; si no hay fila, devuelve `null`. |
| [`getRecords()`](../../src/framework/Model.php#L43) | Clona la consulta, fuerza fetch asociativo y envuelve cada fila en un `ModelRecord`. |
| [`findRecord($id)`](../../src/framework/Model.php#L50) | Añade el filtro de clave primaria mediante `find()` y devuelve el primer registro o `null`. Usa el nombre de clave del esquema cuando existe; sin esquema usa `id`. |

La hidratación usa un clon y `assoc()`, así que `firstRecord()` y `getRecords()` no cambian el fetch mode guardado en la consulta original. Los filtros y límites que ya tenía el modelo sí determinan qué filas hidrata.

Ejemplo de forma de llamada, leído del contrato de código y de los fixtures; no se ejecutó aquí:

```php
$record = UserModel::newInstance(['name' => 'Ana']);
$record->save();

$found = (new UserModel())->findRecord($record->id);
```

Este ejemplo no declara que exista una clase `UserModel` en todas las aplicaciones ni que un esquema o una base de datos estén configurados.

## API de `ModelRecord`

[`ModelRecord`](../../src/framework/ModelRecord.php#L6) guarda el modelo de origen, los atributos actuales, una copia original y un indicador local de persistencia. Las propiedades se leen y escriben mediante `__get()` y `__set()`; `attributes()` entrega el array, `exists()` devuelve el indicador, y `__isset()` sigue la semántica de PHP `isset()` (un atributo con valor `null` no cuenta como establecido).

`save()` tiene dos ramas:

- En un registro nuevo, obtiene un builder de escritura limpio y llama a `create($attributes)`. Si ese método devuelve `false` o `null`, `save()` devuelve `false`; en otro caso añade el identificador devuelto si la clave aún no estaba en los atributos y marca el registro como persistido.
- En un registro persistido, requiere la clave primaria original y rechaza cambiarla. Calcula los campos modificados mediante comparación estricta, quita la clave del conjunto a actualizar y llama a `update()` filtrando por la clave original. Si no hay cambios, devuelve `true`. Si el update informa cero filas, vuelve a leer los campos para distinguir una fila existente sin cambios de una fila ausente o de un valor que no quedó almacenado.

`delete()` devuelve `false` para un registro que no está marcado como persistido. En el otro caso borra por la clave primaria original mediante `Model::delete()`. Como el comportamiento de borrado del modelo puede ser soft delete, `delete()` no significa necesariamente una eliminación física. Un resultado `false` o cero filas se reporta como fallo; con un resultado no cero se actualiza el indicador local a no persistido.

La instancia de escritura se obtiene con [`newRecordQuery()`](../../src/framework/Model.php#L56). Crea un modelo nuevo y copia conexión y configuración de escritura como esquema, fillables, validador, mutators, soft delete y campos de auditoría. No copia `WHERE`, `ORDER BY`, límite, alias de tabla ni prefijo de la consulta de lectura. Si no hay tabla, el registro no puede persistirse; si falta la clave original en una entidad marcada como existente, `save()` o `delete()` lanzan `LogicException`.

## Cobertura observada y límites

`ModelRecordUnitTest.php` contiene casos con dobles para creación y cambios; `SimpleORMTest.php` configura una conexión SQLite `:memory:` y declara casos de create/read/update/delete, clave personalizada, hidratación y persistencia. Los archivos no se ejecutaron en esta revisión. La existencia de esos casos no equivale a una ejecución aprobada ni a compatibilidad con todos los drivers.

El [ledger de Query Builder](../audit/README.md#claim-level-findings-query-builder) registra límites en modos `preview`/`simulate`, borrados, relaciones, transacciones y escrituras anidadas. El estado de los descriptores se desarrolla en [Esquemas](schemas.md). No se afirma aquí un ORM completo, carga automática de relaciones, persistencia transaccional de grafos ni compatibilidad general con otro framework.

---
title: Model y ModelRecord
source:
  - docs/database/model.md
  - docs/database/query-builder.md
source_revision: 683d89509c036f2495e7ba22b2b8a4128273f95c
status: partial
---

# Model y ModelRecord

`Model` compone Query Builder y la configuración de tabla. `ModelRecord` ofrece una API mutable por fila. Son dos caminos disponibles; la presencia de traits de relaciones no prueba que todas las relaciones o escrituras anidadas estén completas.

## Crear un modelo

El constructor de `Model` es `__construct(bool $connect = false, $schema = null, bool $load_config = true)`. Si recibe un esquema, invoca `get()` para obtener tabla, atributos, tipos, reglas y clave primaria. Si `$connect` es verdadero, selecciona/obtiene la conexión activa. `DB::table()` conecta por default, mientras que `new Model()` no.

Cuando se recibe un esquema, el constructor inicializa campos desde `attr_types`, aplica reglas y fillables disponibles y activa soft delete si el esquema contiene `deleted_at`. `setConn()`/`getConn()` permiten inyectar/leer el handle PDO. Los [detalles de conexión](/database) separan selección, apertura diferida y ejecución.

## Hooks

La clase base declara los hooks protegidos `boot()`, `init()`, `onReading()`, `onRead($count)`, `onCreating(&$data)`, `onCreated(&$data, $id)`, `onUpdating(&$data)`, `onUpdated(&$data, $count)`, `onDeleting(&$data)`, `onDeleted(&$data, $count)`, `onRestoring(&$data)` y `onRestored(&$data, $count)`. Son puntos vacíos de extensión en la clase base. Distintos métodos de escritura ejecutan subconjuntos diferentes; no hay una secuencia universal.

## Hidratar registros

| Método | Resultado |
| --- | --- |
| `newInstance($attributes = [], $exists = false)` | Crea un registro; por default no se considera persistido. |
| `firstRecord()` | Clona la consulta, fuerza fetch asociativo y devuelve el registro o `null`. |
| `getRecords()` | Clona la consulta, fuerza fetch asociativo y envuelve cada fila. |
| `findRecord($id)` | Filtra por clave primaria y devuelve primer registro o `null`; sin esquema usa `id`. |

La clonación evita cambiar el fetch mode de la consulta original. Los filtros y límites que ya existen en el modelo determinan qué filas se hidratan.

```php
$record = UserModel::newInstance(['name' => 'Ana']);
$record->save();

$found = (new UserModel())->findRecord($record->id);
```

El nombre `UserModel` es ilustrativo; depende de las clases y esquemas de la aplicación y no se comprobó contra una base de datos en esta auditoría.

## Persistencia con ModelRecord

`ModelRecord` conserva el modelo origen, atributos actuales, copia original e indicador de persistencia. `__get()`/`__set()` leen y escriben atributos; `attributes()` devuelve el array; `exists()` devuelve el indicador. `__isset()` sigue la semántica PHP: una propiedad que vale `null` se considera no establecida.

`save()` en un registro nuevo obtiene un builder de escritura limpio y llama a `create()`. Si el resultado es `false` o `null`, devuelve `false`; en otro caso toma el identificador cuando falta en atributos y marca el registro persistido.

En un registro existente, `save()` exige que esté la clave primaria original y rechaza cambiarla. Calcula cambios mediante comparación estricta y ejecuta `update()` por la clave original. Sin cambios devuelve `true`. Ante cero filas vuelve a leer campos para distinguir fila existente sin cambio, fila ausente o valor que no se guardó.

`delete()` devuelve `false` si el registro no está marcado como persistido. En caso contrario filtra por clave original y delega en `Model::delete()`; puede ser soft delete. Un resultado `false` o cero filas se reporta como fallo, y un resultado no cero cambia el indicador local a no persistido.

El builder de escritura no copia filtros, orden, límite, alias ni prefijo de la consulta lectora; sí copia conexión, esquema y opciones de escritura como fillables, validator, mutators, soft delete y campos de auditoría. Sin tabla no se puede persistir; si falta la clave original, `save()`/`delete()` lanzan `LogicException`.

La superficie pública adicional de `Model` incluye `newRecordQuery()`, `connect()`, `setConn()` y `getConn()`. `ModelRecord` declara `__construct()`, `__get()`, `__set()`, `__isset()`, `attributes()`, `exists()`, `save()` y `delete()`; sus helpers de clave y consulta son privados. `Model` también define hooks protegidos de inicialización, lectura, creación, actualización, borrado y restauración.

`ModelRecordUnitTest.php` contiene dobles para creación y cambios; `SimpleORMTest.php` configura SQLite `:memory:` y contiene casos declarados de CRUD, clave personalizada e hidratación. Se inspeccionaron, no se ejecutaron. No se afirma compatibilidad general por driver ni persistencia transaccional de grafos.

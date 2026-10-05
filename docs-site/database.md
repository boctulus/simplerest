---
title: Base de datos
source:
  - docs/database/README.md
  - docs/database/connections.md
  - docs/database/query-builder.md
  - docs/database/schemas.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

# Base de datos

Esta guía reúne tres alcances distintos: selección de conexiones, un camino de lectura del Query Builder y descriptores de esquema. El código se trazó en fuente; no se ejecutó una consulta ni se probó conectividad o compatibilidad por motor.

## Conexiones

La configuración combina `config/config.php` con `config/databases.php` y define conexiones junto con un identificador predeterminado. Cada entrada requiere `driver` y `db_name`; también puede definir host, puerto, usuario, contraseña, opciones PDO y charset.

`DB::setConnection($id)` selecciona una conexión configurada, pero no abre PDO. `DB::getConnection($id = null)` devuelve el handle cacheado o crea uno de forma diferida. Sin identificador actual, elige la única conexión configurada o el valor por defecto; con varias conexiones y sin default lanza una excepción. El código contiene ramas PDO para `mysql`, `sqlite`, `pgsql` y `sqlsrv`, con aliases `mariadb`, `postgres` y `mssql`. Esto describe ramas de fuente, no extensiones instaladas ni compatibilidad de consultas.

Este ejemplo representa la forma de la API; no se ejecutó contra una base configurada:

```php
DB::setConnection('main');
$pdo = DB::getConnection();
```

El parámetro `$tenant_id` de ciertos métodos `DB` se usa como identificador de conexión configurada. Eso no define aislamiento, propiedad ni un esquema por tenant. Además, `withConnection()` y `withDefaultConnection()` contienen retornos en `finally`, que suprimen una excepción lanzada por el callback.

## Lecturas con Query Builder

`DB::table($from, $alias = null, $connect = true)` resuelve un modelo de aplicación para un nombre de tabla; si recibe una expresión que contiene ` FROM ` usa `MyModel::fromRaw()`. La ruta depende del mapeo de modelos y de una conexión válida.

- `select()` admite campos separados, un array o una cadena separada por comas.
- `where()` admite formas como `where('id', 7)`, `where('id', '>', 2)` y una condición en array.
- `toSql()` construye SQL y bindings; no ejecuta la consulta.
- En la ruta ordinaria de lectura, `get()` prepara, enlaza y ejecuta PDO cuando `$exec` está activo; con ejecución desactivada devuelve `false`.
- Si el esquema del modelo contiene `deleted_at`, se añade `deleted_at IS NULL` salvo que se habiliten los eliminados con `deleted()`.

`dontExec()` evita preparar y ejecutar la sentencia, pero no vuelve offline el camino estándar: `DB::table()` conecta por defecto y el binding puede conectar antes de consultar `$exec`. Cuando `first()` no encuentra fila, la fuente devuelve `[]`, no `null`; ese caso no se probó con una base real.

::: warning Los modos preview/simulate no garantizan ausencia de escrituras
La fuente comprueba algunos modos en `create()` y `executeInsert()`, pero `update()` y `delete()` ejecutan con `$exec` activo sin comprobar el modo seleccionado. No trates `preview()` o `simulate()` como una garantía global de no escritura.
:::

## Descriptores de esquema

El generador produce una clase PHP que implementa `ISchema`; su `get()` devuelve metadatos de tabla, campos, tipos, reglas, clave primaria y relaciones. `make schema` inspecciona una tabla existente y escribe el descriptor: no crea la tabla ni una migración. Parte de la introspección usa SQL orientado a MySQL; un probe aislado con SQLite rechazó `SHOW COLUMNS`. El writer recibe `protected = false`, por lo que la fuente actual permite reemplazar un descriptor existente sin `--force`. No se ejecutó el generador ni DDL.

## Límites actuales

Escrituras, transacciones, consultas raw, paginación, agregados, carga de relaciones, joins automáticos, DDL, migraciones, aislamiento por tenant y compatibilidad por driver siguen fuera del alcance verificado de estas páginas. Los tests de consulta consultados no se ejecutaron; el archivo PHPUnit configurado no incluye `unit-tests/`. Con `debug` activo, una excepción de conexión puede registrar la entrada de configuración seleccionada con valores de credenciales; los secretos no deben exponerse en logs compartidos.

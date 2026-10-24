---
title: Relaciones, joins y subrecursos
source:
  - docs/database/relations.md
  - docs/database/schemas.md
  - docs/audit/README.md
source_revision: e0a3dc3f5843be258eed7d5cc1edec13031d5f87
status: partial
---

# Relaciones, joins y subrecursos

Esta referencia traza la resolución de relaciones desde el esquema. No se ejecutó una consulta relacional, un escaneo de pivotes ni una lectura desde la base de datos.

## Joins inferidos

`join($tabla)` con ambos campos `ON` omitidos requiere el esquema del modelo y su metadata de relaciones. Para relaciones directas usa `expanded_relationships`; cuando hay varias relaciones entre las mismas tablas, genera aliases. Si no encuentra una relación directa, revisa si existe un pivote y une la tabla puente con sus foreign keys. `joinTo(...$tablas)` invoca `join()` para cada tabla y conserva esos requisitos.

Si se pasan las claves `ON`, el builder arma un join explícito. `leftJoin()` y `rightJoin()` seleccionan el tipo de join nombrado; los métodos raw, cross y natural siguen otras rutas y no implican una inferencia automática.

## `connectTo()` carga subrecursos

`connectTo([...])` habilita la calificación de tablas y guarda la lista relacionada. Cuando `get()` o `first()` ve una lista no vacía, delega a `SubResourceHandler::getSubResources()`: genera subconsultas correlacionadas y agregados tipo JSON, no filas aplanadas de un join tradicional.

El handler descodifica los valores relacionados y pasa el resultado por los output mutators y transformer del modelo base. Un alias de relación directa puede derivarse de un campo como `subdivision_id`; el alias de una relación con pivote se pluraliza. El SQL y la forma de los resultados dependen de los metadatos y funciones del motor configurado. Con la ejecución desactivada, el handler devuelve el SQL generado, pero todavía llama a `DB::table($tablaRelacionada)` con conexión por defecto para leer los campos visibles. `dontExec()` no garantiza que esta ruta evite abrir PDO.

::: warning La caché omite filtros
La clave de caché estática incluye tabla base, tablas relacionadas, identificador de conexión y `pristine`; no incluye los filtros de la consulta base ni `subquery_conditions`. Dos llamadas con condiciones distintas pueden compartir una entrada en el mismo proceso. No se ejecutó una lectura para reproducir este caso.
:::

## Pivotes y escrituras anidadas

`DBRels::getPivot()` lee `Pivots.php`. Si falta, intenta invocar `MakePivotScanCommand` cuando esa clase está disponible; si no, lanza una excepción con una instrucción de preparación. Un join inferido muchos-a-muchos puede alcanzar esa ruta. El comando puede generar archivos de esquema; no se invocó.

`Model` también compone `InsertWithSubResourcesTrait`, pero `insertStruct()` está incompleto y llega a un debug dump antes de implementar un flujo íntegro de escritura anidada. `connectTo()` describe una ruta de lectura y no habilita inserts anidados.

`SubResourceHandlerTest.php` contiene dos aserciones SQL para relaciones directas. Se inspeccionaron sin ejecutar; no prueban pivotes, forma de datos en runtime, integridad de caché o compatibilidad por motor. Consulta los límites en [compatibilidad por driver](/database/driver-compatibility). Los hallazgos internos del audit ledger permanecen en la fuente canónica.

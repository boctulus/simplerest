---
title: API Collections
source:
  - docs/api/collections.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# API Collections

`Collections` guarda un conjunto de IDs de una entidad para actualizarlos o eliminarlos después. `ApiHandler` reserva el slug `collections` y lo resuelve al controlador incorporado. Su contrato es distinto del CRUD genérico descrito en [Endpoints automáticos](/api/automatic-endpoints).

## Colección guardada

`POST /api/{version}/collections` espera JSON con `entity` y `refs`. El controlador resuelve el modelo y rechaza tablas de ACL, carpetas, roles y permisos de usuario, además de la tabla de usuarios configurada. Guarda la entidad, las referencias codificadas como JSON y el ID del usuario actual en `collections`. Si la operación funciona, devuelve estado 201 con el ID de la colección.

## Actualizar los registros referenciados

`PUT` y `PATCH` reciben el ID de colección en la ruta. El controlador carga esa colección, comprueba que pertenezca al usuario actual salvo que tenga `write_all_collections`, resuelve el modelo guardado y valida los campos enviados con las reglas de ese modelo. Sin el permiso especial, el modelo destino debe tener `belongs_to` y la actualización queda limitada al usuario.

La actualización normal ocurre en una transacción: modifica los IDs guardados y elimina la fila de colección. El campo `refs` del cuerpo se ignora; se usa la lista almacenada. En cambio, la rama `remove=true` borra directamente la fila de colección sin la comprobación de propietario o `write_all_collections` que aplica la rama de actualización. El código fuente no demuestra protección de propiedad en ese camino.

## Eliminación por lote

`DELETE /api/{version}/collections/{id}?entity={entity}` requiere el ID de colección y un `entity` que coincida con el valor guardado. Comprueba propietario o `write_all_collections`, elimina los IDs mediante el modelo dentro de una transacción y borra la fila de colección. Sin el permiso especial, el modelo debe exponer `belongs_to` para acotar la operación al usuario actual.

El constructor agrega métodos de colección a su lista de callables cuando detecta una credencial; la validez depende igualmente de `auth()->check()`. El controlador aplica además las comprobaciones propias descritas arriba. No se reprodujo una autorización respaldada por base de datos.

## Alcance de verificación

Se inspeccionaron el código y los tests de `ApiCollections` y validación de entidad, pero no se ejecutaron. No se creó, cambió ni eliminó ninguna colección o registro referenciado.

---
title: API TrashCan
source:
  - docs/api/trashcan.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# API TrashCan

`TrashCan` permite consultar registros ya marcados como eliminados lógicamente. `ApiHandler` reserva el slug `trashcan` y normaliza también `trash_can` hacia el mismo controlador. Necesita un controlador API y un modelo de aplicación para la entidad indicada.

## Entidad y requisitos de esquema

La entidad se toma del header `entity`, del query string o del body, en ese orden. El controlador requiere un controlador API de la aplicación, que `hasSoftDelete()` devuelva `true` y que el modelo resuelva la columna de fecha de eliminación.

## Lectura, restauración y eliminación permanente

`GET /api/{version}/trashcan/{id?}` activa `showDeleted()` y filtra registros cuya fecha de eliminación no sea nula. Si corresponde el scope normal del propietario, la lista usa el campo `belongs_to`. Si el modelo carece de ese campo, la lectura requiere `read_all_trashcan`.

`POST` se rechaza con estado 405. `PUT` y `PATCH` usan la actualización del controlador base; enviar `trashed: false` (booleano o texto `"false"`) limpia la fecha y restaura el registro. Otros valores no activan esa rama de restauración. Si el modelo no tiene campo de propietario, las escrituras requieren `write_all_trashcan`.

`DELETE /api/{version}/trashcan/{id}` selecciona un registro que ya está en la papelera y desactiva el soft-delete para esa operación; el código solicita su eliminación permanente. No se ejecutó la operación en una base de datos.

Los permisos `read_all_trashcan` y `write_all_trashcan` son controles propios de este controlador. Consulta [ACL](/security/acl), [Endpoints automáticos](/api/automatic-endpoints) y [Model](/database/model).

## Alcance de verificación

Se inspeccionó el código y el test `ApiTrashCanTest`, pero no se ejecutó. No se leyó, restauró ni eliminó permanentemente ningún registro.

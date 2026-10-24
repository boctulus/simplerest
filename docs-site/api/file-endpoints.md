---
title: Endpoints de archivos
source:
  - docs/api/file-endpoints.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# Endpoints de archivos

La aplicación declara rutas de WebRouter para metadatos, subida, eliminación y descarga en `config/routes/files.php`. Construyen las clases `Core\Api\Files` y `Core\Api\Download`. Este flujo es independiente del resolver API genérico y de los [helpers de filesystem y uploads](/core/file-uploads).

## Rutas declaradas

| Método y ruta | Acción | Comportamiento trazado |
| --- | --- | --- |
| `GET /api/v1/files` | `Files::get()` | Devuelve filas de metadatos. |
| `GET /api/v1/files/{id}` | `Files::get($id)` | Devuelve una fila de metadatos. |
| `POST /api/v1/files` | `Files::post()` | Recibe uploads multipart mediante `FileUploader`, crea filas en `files` y devuelve estado 201 con IDs y errores por archivo. |
| `DELETE /api/v1/files/{id}` | `Files::delete($id)` | Ejecuta la ruta de borrado de fila y archivo físico. |
| `GET /download/{id}` | `Download::get($id)` | Sirve el archivo como adjunto y termina el proceso. |

Las rutas corresponden al archivo de esta aplicación; no se ha demostrado que otro consumidor incluya ese archivo.

## Subida y asociación opcional

El controlador usa `FileUploader` y guarda nombre original, extensión, nombre almacenado, propietario y el valor enviado para `guest_access` en la tabla `files`. El permiso especial `transfer` permite asignar `belongs_to`; en los otros casos, el dueño es el usuario autenticado o queda nulo para un guest.

El campo multipart opcional `entity` selecciona un controlador de aplicación que debe implementar `attachUploadedFile($uuid, $data)`. El endpoint también exige `write_all` o el permiso `create` de esa entidad. Si falla la asociación, intenta retirar el metadato y el archivo recién subido.

Hay una discrepancia de ruta: la respuesta de `Files::post()` construye `/get/{id}`, pero la ruta declarada es `/download/{id}`. No se encontró una ruta `/get/{id}` en los archivos de rutas inspeccionados.

## Lectura y descarga: límites de autorización

`Files::get()` devuelve metadatos; no sirve el archivo. Las comparaciones de propietario en `get()` y `delete()` sólo entran cuando el esquema no informa `belongs_to`; si ese campo existe, la comparación se omite. Este código no demuestra enforcement de propietario en esa rama.

El controlador tiene soft-delete desactivado. El borrado intenta quitar primero el archivo físico y después llama al método de eliminación del modelo; ambas operaciones no están envueltas juntas en una transacción.

`Download::get()` consulta el nombre almacenado, fija headers de adjunto, ejecuta `readfile()` y termina. Para guest agrega `guest_access = 1` sólo si la tabla contiene esa columna; la propiedad estática `$guest_access` no se consulta en ese método. Para un usuario autenticado sin `read_all`, el lookup agrega el ID del propietario actual. El acceso efectivo depende del esquema y del ACL de la aplicación.

## Alcance de verificación

Se inspeccionaron las rutas, clases y helper; no se ejecutó ningún upload, consulta, eliminación o descarga.

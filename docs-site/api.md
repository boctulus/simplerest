---
title: API HTTP
source:
  - docs/api/README.md
  - docs/api/automatic-endpoints.md
source_revision: cd60be5f8416cdcbbe7011e07486c4b65c0e0df2
status: partial
---

# HTTP/API

Esta página cubre la resolución de recursos y la preparación del despacho API que se trazaron en fuente. No presenta un CRUD completo como flujo comprobado.

## Resolución de recursos

Con `remove_api_slug = false`, la configuración espera un segmento con forma de versión después del prefijo `api`. Para un slug ordinario, `ApiHandler` deriva una clase de aplicación bajo el namespace `Controllers\api`; `trashcan` y `collections` usan mapeos especiales a controladores core.

El resolver devuelve la clase destino, el método, los argumentos y la versión. No busca esquemas en la base de datos ni crea controladores automáticamente. `FrontController` comprueba la existencia de clase y método y, para requests API que no sean de autenticación, exige que el método figure en `getCallable()`.

## Scaffold y modelos

El comando `make api` escribe un scaffold de controlador basado en `ApiRestfulController`, que extiende el `ApiController` core y declara métodos genéricos GET, POST, PUT, PATCH y DELETE. El scaffold establece literalmente su indicador de soft-delete en `true`; no lo deriva del esquema. Si no se configura un modelo explícito, `ApiController` deriva su nombre desde la clase de controlador y pide la instancia del modelo usando el mapeo de la conexión activa.

Generar un esquema no crea por sí solo el controlador ni el modelo. Aunque existan esas piezas, el resultado depende de autenticación, ACL, resolución de esquemas, conexión válida y ruta de request. No se ejecutó el generador combinado ni una secuencia CRUD limpia.

## Estado de autorización y cobertura

El controlador de recursos incorporado comprueba credenciales y calcula acciones invocables antes de despachar. La página de [Seguridad](/security) describe esa frontera y distingue el mecanismo core de la configuración de aplicación.

La referencia de [endpoints automáticos](/api/automatic-endpoints) documenta la conversión de parámetros GET y los métodos de escritura trazados en el código, con sus límites por rama. Es una descripción de fuente: no se ejecutó una petición HTTP respaldada por base de datos ni un recorrido CRUD de extremo a extremo. Serialización, validación y autorización siguen requiriendo reproducción funcional.

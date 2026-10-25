---
title: API HTTP
source:
  - docs/api/README.md
  - docs/api/automatic-endpoints.md
  - docs/api/resource-controllers.md
  - docs/api/collections.md
  - docs/api/trashcan.md
  - docs/api/file-endpoints.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
---

# HTTP/API

SimpleRest ofrece controladores reutilizables para recursos REST y métodos HTTP de lectura y escritura. Los endpoints pueden incorporar filtros, agregaciones, relaciones y comprobaciones ACL.

## Resolución de recursos

Con `remove_api_slug = false`, la configuración espera un segmento con forma de versión después del prefijo `api`. Para un slug ordinario, `ApiHandler` deriva una clase de aplicación bajo el namespace `Controllers\api`; `trashcan` y `collections` usan mapeos especiales a controladores core.

El resolver devuelve la clase destino, el método, los argumentos y la versión. No busca esquemas en la base de datos ni crea controladores automáticamente. `FrontController` comprueba la existencia de clase y método y, para requests API que no sean de autenticación, exige que el método figure en `getCallable()`.

## Scaffold y modelos

`php com make api products` genera un controlador REST basado en `ApiRestfulController`, que hereda GET, POST, PUT, PATCH y DELETE de `ApiController`. También se pueden generar conjuntamente el esquema, modelo y controlador:

```bash
php com make any products --schema --model --api --from=main
```

El controlador puede resolver su modelo por convención de nombres. La opción de borrado lógico del controlador requiere un campo apropiado en el esquema.

Un esquema aislado no registra automáticamente el controlador. Para utilizar los endpoints son necesarios los artefactos PHP correspondientes, la conexión de base de datos y la autorización del recurso.

## Estado de autorización y cobertura

El controlador de recursos incorporado comprueba credenciales y calcula acciones invocables antes de despachar. La página de [Seguridad](/security) describe esa frontera y distingue el mecanismo core de la configuración de aplicación.

La referencia de [endpoints automáticos](/api/automatic-endpoints) cubre parámetros GET y ramas de escritura. Las guías de [controladores de recursos](/api/resource-controllers), [Collections](/api/collections), [TrashCan](/api/trashcan) y [archivos](/api/file-endpoints) separan sus contratos y límites.

Consulta las guías correspondientes para conocer los parámetros admitidos, los permisos aplicables y las restricciones propias de cada operación.

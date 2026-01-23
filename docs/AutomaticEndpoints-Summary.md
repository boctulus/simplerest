# Resumen Ejecutivo: Sistema de Endpoints Automáticos de SimpleRest

SimpleRest permite la exposición automática de recursos (tablas de base de datos) como endpoints RESTful sin necesidad de escribir código adicional para cada entidad, siguiendo el principio de **Convención sobre Configuración**.

## Arquitectura y Flujo de Trabajo

1.  **Enrutamiento Dinámico**: El `FrontController` actúa como un "catch-all" para peticiones que no coinciden con rutas manuales. Utiliza el `ApiHandler` para interpretar URLs con el formato `/api/{version}/{recurso}`.
2.  **Resolución de Controladores**: El sistema busca un controlador en `app/Controllers/api/` que coincida con el nombre del recurso (ej. `products` -> `ProductsController`). 
3.  **Controlador Base Inteligente**: Los controladores extienden `MyApiController` (y este a su vez a `ApiController` del core). El `ApiController` centraliza toda la lógica de CRUD.
4.  **Descubrimiento de Modelos**: El controlador identifica automáticamente su modelo asociado basado en su propio nombre (ej. `ProductsController` -> `ProductsModel`), lo que le permite interactuar con la tabla `products` de forma inmediata.

## Capacidades de los Endpoints

Los endpoints automáticos soportan una amplia gama de operaciones y filtros a través de parámetros de consulta (Query Strings):

*   **Filtros Avanzados**: Soporta operadores como `eq`, `neq`, `gt`, `gteq`, `lt`, `lteq`, `contains`, `startsWith`, `endsWith`, `in`, `between`, entre otros.
    *   Ejemplo: `?price[gteq]=100&name[contains]=jugo`
*   **Proyección de Campos**: `fields` para incluir y `exclude` para omitir campos.
*   **Paginación**: Soporta `page` y `pageSize`, o `limit` y `offset`.
*   **Ordenamiento**: `orderBy[field]=ASC|DESC`.
*   **Agregación y Agrupamiento**: Soporta funciones como `sum()`, `avg()`, `count()` y cláusulas `groupBy` y `having`.
*   **Sub-recursos**: Permite incluir datos relacionados usando `include` o `_related`.

## Seguridad y ACL

Cada petición pasa por un sistema de **ACL (Access Control List)** granular:
*   Verifica permisos a nivel de recurso (`list`, `show`, `create`, `update`, `delete`).
*   Soporta permisos "All" (ej. `read_all`) para ver registros de otros usuarios.
*   Integra el sistema de **Folders** para compartir recursos entre usuarios de forma controlada.

## Notas Técnicas
*   **Versioning**: El sistema está preparado para versionado de API (v1, v2, etc.) desde la estructura de directorios y herencia de clases.
*   **Extensibilidad**: Se pueden sobreescribir métodos específicos en los controladores de `app/Controllers/api/` para personalizar el comportamiento por defecto sin romper la automatización.

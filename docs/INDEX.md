# Documentación de SimpleRest

La documentación fuente se escribe en español. El español ocupa la raíz y será el idioma predeterminado del sitio. Las traducciones se derivarán bajo rutas como `/en/` e `/it/`; no se mantendrán como fuentes independientes. Si hay una discrepancia, prevalece la página española.

La migración al español es progresiva. Algunas páginas auditadas siguen en inglés y se enlazan aquí por su contenido vigente; se traducirán al actualizarlas. Las páginas antiguas no enlazadas desde este mapa son material de auditoría, no especificaciones actuales.

## Empieza aquí

- [Arquitectura](architecture.md) — entrada de la aplicación, bootstrap y relación entre los routers y el front controller.
- [Primeros pasos](getting-started/README.md) — estado comprobado de instalación y bootstrap. Todavía no es un tutorial de instalación reproducido de principio a fin.

## Documentación por tema

| Tema | Páginas actuales | Alcance y límites |
| --- | --- | --- |
| Núcleo | [Core](core/README.md), [ciclo de petición](core/request-lifecycle.md) | Trazado de código para el despacho y el ciclo de petición; varias APIs de Request, Response y middleware siguen pendientes. |
| Base de datos | [Base de datos](database/README.md), [conexiones](database/connections.md), [Query Builder](database/query-builder.md), [esquemas](database/schemas.md) | Evidencia de código para selección de conexiones, lectura y descriptores de esquema; conectividad, escrituras, transacciones y compatibilidad por motor siguen pendientes. |
| HTTP y API | [API HTTP](api/README.md), [resolución de recursos](api/automatic-endpoints.md) | Se documenta el despacho desde el código; no se ha reproducido un flujo completo de generación y CRUD. |
| Seguridad | [Autenticación](security/authentication.md), [ACL](security/acl.md) | Alcance de código y pruebas focalizadas con límites explícitos; falta una petición de autorización respaldada por la base de datos. |
| CLI | [Estado de CLI](cli/README.md) | Falta inventariar comandos y reproducir sus instrucciones. |
| Integraciones | [Estado de integraciones](integrations/README.md) | La presencia de un paquete no confirma por sí sola un contrato de integración. |
| Despliegue | [Estado de despliegue](deployment/README.md) | Aún no hay instrucciones validadas de despliegue. |
| Referencia | [Estado de referencia](reference/README.md) | Falta construir el catálogo de configuración, variables, comandos y métodos públicos. |

## Cómo interpretar el estado

Las páginas distinguen entre evidencia de implementación, configuración, pruebas y ejecución reproducida. Que una clase o método exista no demuestra que un tutorial completo funcione. El registro interno de auditoría conserva afirmaciones rechazadas, límites y trabajo pendiente.

## Material histórico e interno

`audit/` conserva evidencia y páginas pendientes; `_internal/legacy/` conserva índices y material histórico. Estas carpetas no son documentación publicable. La futura compilación del sitio también debe excluir `framework/` y las páginas legacy de la raíz que no estén enlazadas desde este mapa.

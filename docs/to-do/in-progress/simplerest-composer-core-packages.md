---
title: "SimpleRest: microcore limpio y paquetes Composer"
current_step: 8
next_step: 8
parallelizable_steps: []
parent: null
global_complexity: extreme
for_agents: false
next_step_complexity: medium
tags: [composer, architecture, microcore, testing]
---
## Contrato de alcance vigente

El paquete raíz conserva la identidad `boctulus/simplerest`; no crear otro core ni cambiar tags publicados. El core debe contener únicamente arranque HTTP, routing, middleware genérico y respuestas JSON. Las extensiones se cargan solo por decisión explícita del consumidor mediante proveedores genéricos. No se admiten aliases, shims, fallbacks con `class_exists`, referencias a nombres concretos de paquetes, ni carga opcional automática. Bootstrap/AdminLTE queda fuera del núcleo y se conserva únicamente en la extensión legacy existente si sus contratos pasan validación.

No modificar GoProp ni publicar paquetes o versiones en Packagist durante esta tarea. La extracción o el archivo de cada archivo del inventario exige revisar sus consumidores antes del movimiento; el CSV no sustituye ese análisis.

## Estado de implementación

- `src/framework/` quedó reducido a 14 clases genéricas de HTTP, routing, middleware, DI, errores y configuración en memoria. Los 239 candidatos de extracción se conservaron bajo `archive/simplerest-framework-extractions-2026-10-23/`; ese directorio no se autoload y queda fuera de los artefactos Composer.
- Se eliminaron los 17 elementos clasificados para borrado, incluidos aliases y shims. El manifiesto publicable requiere únicamente PHP y expone solo el namespace del microcore. PHPUnit del monorepo se declara por separado en `composer-dev.json`.
- La extracción semántica de Graphify respondió HTTP 401; se conservó únicamente el mapa AST. Ese mapa no reemplaza la revisión de consumidores ni demuestra que los paquetes de extensión actuales sean compatibles con el microcore.
- `php scripts/distribution/verify-release-artifacts.php --microcore-only` pasó: el ZIP contiene 17 archivos, instala como paquete local aislado, responde una petición HTTP real `GET /health` con JSON y devuelve 503 para middleware protegido ausente.
- Pasaron las 4 pruebas focalizadas del microcore (19 aserciones), la prueba del manifiesto del empaquetador (9 aserciones), la validación Composer del paquete y el guard documental.
- El mapa semántico de consumidores no se pudo completar y los paquetes/extensiones existentes y el bootstrap monolítico aún no se migraron al microcore. Sus tags siguen intactos; no se publicaron versiones nuevas ni se creó tag para el core.

## Pasos planificados

1. **Trazar el kernel y consumidores** — revisar el grafo de `src/framework/`, usos en `app/`, `config/`, paquetes locales y pruebas para fijar los símbolos que necesita una petición HTTP/JSON mínima.
2. **Eliminar aliases y shims** — borrar el cargador de 18 aliases y los 16 shims físicos, retirar su `autoload.files` y eliminar cualquier referencia de compatibilidad desde el core.
3. **Desacoplar el kernel HTTP** — dejar `Request`, `Response`, `FrontController`, `WebRouter`, `OutputHandler`, `Config` y `Factory` sin lógica de DB, API, ACL, autenticación, consola ni resolución de clases de aplicación.
4. **Separar bootstrap y helpers** — mover rutas de aplicación y carga de helpers al bootstrap del consumidor; eliminar la carga conjunta de helpers opcionales y el artefacto PHP inválido `Collections.php` tras verificar referencias.
5. **Extraer por responsabilidad** — mover o archivar DB/ORM, REST automático, auth/ACL, PSR-7, vistas/HTML, Bootstrap legacy, correo, webhooks, CLI, generadores, integraciones y utilidades según dependencias verificadas y destinos existentes.
6. **Adelgazar Composer** — retirar dependencias runtime y `suggest` de extensiones del core; separar los mapas `autoload-dev` del workspace para que el manifiesto de `boctulus/simplerest` describa solo el microcore.
7. **Cerrar pruebas de aislamiento** — probar una instalación `--no-dev` con solo `boctulus/simplerest`, `GET /health` JSON, ausencia de referencias/aliases/cargas opcionales, y fallo seguro de rutas protegidas sin proveedor.
8. **Actualizar la auditoría** — sustituir las propuestas por estados verificados, actualizar el inventario de documentación solo si cambian rutas/enlaces y correr el guard documental.

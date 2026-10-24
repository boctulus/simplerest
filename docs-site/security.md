---
title: Seguridad
source:
  - docs/security/authentication.md
  - docs/security/acl.md
  - docs/security/jwt.md
  - docs/security/acl-implementations.md
  - docs/security/authorization-engine.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# Seguridad

Esta sección separa autenticación de autorización y distingue el mecanismo del framework de la política elegida por cada aplicación. Las referencias detalladas describen ramas trazadas en el código; sus límites de ejecución permanecen visibles.

## Dos controles distintos

La [referencia de autenticación](/security/authentication) cubre detección y validación de JWT y API keys, resolución de identidad, rutas, proveedor `IAuth` y códigos observados o trazados. Presencia de credencial y validez son conceptos distintos.

La guía de [JWT](/security/jwt) separa los tokens de acceso, renovación y email, y documenta que el controlador procede del paquete opcional `simplerest-jwt-auth`.

La [referencia de ACL y autorización API](/security/acl) describe roles, permisos de recurso y especiales, fuentes por usuario, composición de callables del API y sus límites. `Factory::acl()` carga la selección de aplicación; el core no decide por sí solo una política concreta.

Las [implementaciones ACL](/security/acl-implementations) distinguen BasicACL y FineGrainedACL; el [motor de autorización](/security/authorization-engine) cubre snapshots, policies, jerarquía y máscaras de permisos.

## Lecturas relacionadas

- [HTTP/API](/api) y [resolución de endpoints automáticos](/api/automatic-endpoints) explican el despacho de recursos.
- El [ciclo HTTP](/core/request-lifecycle) queda separado de la autenticación y la autorización a nivel de controlador.
- Las guías separan el código core, los paquetes opcionales y la configuración de aplicación. No se reprodujo autorización end-to-end con identidad y permisos de base de datos.

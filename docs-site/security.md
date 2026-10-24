---
title: Seguridad
source:
  - docs/security/authentication.md
  - docs/security/acl.md
source_revision: 7cb44199e717e2485f0b1bee357ccaa402016412
status: partial
---

# Seguridad

Esta sección separa autenticación de autorización y distingue el mecanismo del framework de la política elegida por cada aplicación. Las referencias detalladas describen ramas trazadas en el código; sus límites de ejecución permanecen visibles.

## Dos controles distintos

La [referencia de autenticación](/security/authentication) cubre detección y validación de JWT y API keys, resolución de identidad, rutas, proveedor `IAuth` y códigos observados o trazados. Presencia de credencial y validez son conceptos distintos.

La [referencia de ACL y autorización API](/security/acl) describe roles, permisos de recurso y especiales, fuentes por usuario, composición de callables del API y sus límites. `Factory::acl()` carga la selección de aplicación; el core no decide por sí solo una política concreta.

## Lecturas relacionadas

- [HTTP/API](/api) y [resolución de endpoints automáticos](/api/automatic-endpoints) explican el despacho de recursos.
- El [ciclo HTTP](/core/request-lifecycle) queda separado de la autenticación y la autorización a nivel de controlador.
- Los hallazgos de fuente, pruebas y límites se mantienen en el ledger canónico; esta sección pública no presenta una reproducción end-to-end con identidad y permisos de base de datos.

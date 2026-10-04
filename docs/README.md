# Documentación de SimpleRest

SimpleRest separa la **fuente editorial canónica** de sus versiones publicables. La fuente canónica conserva hechos, intención de diseño, decisiones y contexto bajo autoridad del maintainer. La versión pública en español y las traducciones son derivados de esa fuente. La política completa está en [Gobernanza de la documentación](documentation-governance.md).

El mapa actual de documentación publicable es [`index.md`](index.md); este archivo sirve como entrada desde GitHub y remite al mismo mapa.

## Regla de idioma y derivación

- El español será el idioma predeterminado del sitio público.
- La versión pública en español se deriva de la fuente editorial canónica; no sustituye su autoridad.
- Las traducciones viven bajo rutas como `en/` e `it/` y se derivan del mismo corpus canónico.
- Si una versión derivada difiere de la fuente canónica, prevalece la fuente canónica.
- Las páginas técnicas que todavía están en inglés se revisarán durante la migración; no deben evolucionar como fuentes independientes.

## Autoridad y estado

El código, la configuración y las pruebas actuales definen el comportamiento implementado. La fuente editorial canónica también puede conservar intención, contratos conceptuales y decisiones que el código por sí solo no expresa. Una discrepancia entre código y documentación no autoriza una corrección automática: debe determinarse si hay documentación obsoleta, una regresión, una implementación incompleta o una decisión aún no implementada.

Los ejemplos de uso se consideran ejecutables solo cuando se han reproducido en el entorno indicado. La [auditoría documental](audit/README.md) guarda evidencia y pendientes; es material interno y no forma parte de la documentación publicable.

Solo las páginas enlazadas desde [`index.md`](index.md) forman actualmente el mapa de documentación publicable. Las páginas históricas no enlazadas que estaban en la raíz de `docs/` y en el antiguo árbol público `docs/framework/` se movieron a `_internal/legacy/`; el índice histórico de mayo de 2026 también se conserva allí en [`INDEX-2026-05.md`](_internal/legacy/INDEX-2026-05.md).

Cuando se configure VitePress, la raíz corresponderá al locale español y las traducciones irán bajo sus prefijos de idioma. El build excluirá `audit/`, `_internal/`, `framework/` y cualquier página legacy no enlazada desde `index.md`.

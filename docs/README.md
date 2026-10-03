# Documentación de SimpleRest

La documentación fuente de SimpleRest se escribe en español. El mapa canónico es [`index.md`](index.md); este archivo sirve como entrada desde GitHub y remite al mismo mapa.

## Regla de idioma

- El español es el idioma fuente y el idioma predeterminado de la documentación.
- Las traducciones viven bajo rutas como `en/` e `it/` y se derivan de las páginas fuente en español.
- Si una traducción difiere de su fuente, prevalece la versión española.
- Las páginas técnicas que todavía están en inglés se traducirán durante su revisión; no deben evolucionar como fuentes independientes.

## Autoridad y estado

El código, la configuración y las pruebas actuales definen el comportamiento. Los ejemplos de uso se consideran ejecutables solo cuando se han reproducido en el entorno indicado. La [auditoría documental](audit/README.md) guarda evidencia y pendientes; es material interno y no forma parte de la documentación publicable.

Solo las páginas enlazadas desde [`index.md`](index.md) son la documentación canónica actual. Las páginas temáticas antiguas en la raíz de `docs/` y las páginas de `docs/framework/` son material de auditoría hasta que se revisen y se incorporen al mapa. El índice histórico de mayo de 2026 se conserva en [`_internal/legacy/INDEX-2026-05.md`](_internal/legacy/INDEX-2026-05.md).

Cuando se configure VitePress, la raíz corresponderá al locale español y las traducciones irán bajo sus prefijos de idioma. El build excluirá `audit/`, `_internal/`, `framework/` y cualquier página legacy no enlazada desde `index.md`.

---
title: "Documentación: proteger la fuente editorial canónica"
current_step: 8
next_step: null
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: true
next_step_complexity: null
tags: [documentation, governance, canonical, ai, publishing]
---

## Objetivo

Convertir la política definida en `docs/documentation-governance.md` en un mecanismo operativo y verificable que proteja la fuente editorial canónica de SimpleRest frente a reescrituras amplias, pérdidas de intención y confusión con documentación pública derivada.

La fuente editorial canónica conserva no sólo hechos técnicos sino también intención de diseño, decisiones arquitectónicas, contratos conceptuales y contexto del maintainer. El código sigue siendo autoridad sobre el comportamiento implementado, pero una discrepancia código/documentación no debe resolverse reescribiendo automáticamente la documentación.

## Restricciones editoriales

- La IA puede hacer cambios localizados en documentación canónica: correcciones concretas, ejemplos, nombres de clases/rutas/comandos, enlaces, aclaraciones, secciones nuevas y marcas de incertidumbre.
- La IA no debe reescribir un documento canónico completo ni una sección extensa sólo para uniformar estilo o tono.
- No se debe eliminar contexto, intención, razonamiento o decisiones históricas por considerarlos redundantes.
- Una contradicción entre código y documentación debe clasificarse antes de modificar la fuente canónica: documentación obsoleta, regresión, implementación incompleta, intención aún no implementada o evidencia insuficiente.
- Reescrituras sustanciales, eliminación, división, fusión o reemplazo de documentos canónicos requieren justificación y aprobación explícita del maintainer.
- La documentación pública en español y las traducciones son derivados regenerables y no sustituyen a la fuente canónica.

## Pasos planificados

1. **Relevar y declarar el corpus canónico actual** — revisar las páginas enlazadas desde `docs/index.md`, el registro de auditoría y los documentos todavía pendientes de clasificación; producir un manifest explícito de documentos canónicos sin mover ni reescribir contenido.
2. **Definir el contrato de protección automático** — diseñar qué cambios deben permitirse, advertirse o bloquearse sobre archivos canónicos: delete, rename/move, reemplazo masivo y reducción sustancial de contenido; definir también el mecanismo explícito de override aprobado por el maintainer.
3. **Implementar el guard documental** — crear un script reutilizable que compare el diff con el manifest y falle ante operaciones destructivas o reescrituras amplias no aprobadas, sin bloquear ediciones localizadas normales.
4. **Integrar la gobernanza en los agentes** — añadir una referencia obligatoria a `docs/documentation-governance.md` y al guard en las instrucciones/skills pertinentes para que Codex y otros agentes consulten la política antes de modificar documentación canónica.
5. **Clasificar el resto de `docs/`** — inventariar cada Markdown como `canonical`, `legacy`, `internal/audit` o `needs-review`, registrando además referencias entrantes/salientes y enlaces rotos antes de mover archivos.
6. **Cuarentenar legacy por lotes pequeños** — mover únicamente documentos ya clasificados a `docs/_internal/legacy/` o `docs/audit/pending/`, actualizando referencias y verificando enlaces después de cada lote. No reescribir su contenido durante el movimiento.
7. **Cerrar la topología documental** — comprobar que `docs/index.md` sea el único mapa público, que ningún material legacy aparezca como especificación vigente y que el árbol canónico quede listo para generar una versión pública española derivada.
8. **Definir el pipeline de derivados** — documentar cómo se generará o mantendrá la versión pública española y, a partir del mismo corpus canónico, las traducciones (`en`, `it`, `pt`, etc.), dejando claro que esos artefactos son regenerables.

## Entregables esperados

- Manifest explícito del corpus canónico.
- Guard automático contra borrado/rename/reescritura masiva no aprobada.
- Instrucciones de agentes enlazadas a la política de gobernanza.
- Inventario completo `canonical / legacy / internal / needs-review`.
- Legacy físicamente separado sin pérdida de contenido.
- Navegación canónica sin enlaces rotos hacia material retirado.
- Contrato documentado para generar publicación ES y traducciones sin convertirlas en nuevas fuentes de verdad.

## Criterios de aceptación

- Existe una lista inequívoca y versionada de documentos canónicos.
- Una edición localizada de una página canónica sigue siendo posible sin fricción especial.
- Un borrado, rename o reescritura sustancial de una página canónica falla salvo override explícitamente aprobado.
- El guard no decide que el código tiene razón ante una contradicción documental; sólo protege el corpus y obliga a clasificar la discrepancia.
- Los agentes tienen una instrucción visible y obligatoria que prohíbe regenerar libremente documentos canónicos.
- Todo Markdown bajo `docs/` queda clasificado o marcado `needs-review`.
- El material legacy queda físicamente separado o explícitamente pendiente, sin confundirse con documentación vigente.
- `docs/index.md` queda como único mapa canónico público.
- La documentación pública española y las traducciones quedan definidas como derivados regenerables de la fuente canónica.

## Continuidad

Los pasos 1–8 están completados: el corpus canónico está protegido, los documentos están clasificados y el legacy está separado sin reescrituras. Los cinco Markdown de webhooks se revisaron sólo durante el lote final y quedaron clasificados; sus cambios técnicos se difieren al bloque posterior que corresponda. No se instaló VitePress. La siguiente etapa del roadmap es cerrar los gaps técnicos de Getting Started, CRUD, DB, CLI y deployment antes de generar la publicación española y las traducciones.

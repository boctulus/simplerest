---
title: Audit environment credential history by branch and commit
current_step: 6
next_step: null
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: false
next_step_complexity: null
tags: [security, credentials, git-history]
---
## Pasos planificados

1. **Inventariar refs** — Capturar branches locales, heads remotos publicados y refs remotas locales antiguas, con sus commits actuales.
2. **Enumerar commits y rutas** — Recorrer cada commit alcanzable por cada ref y registrar solo `.env`, `.env.example` en cualquier directorio y `config/config.php` en la raíz.
3. **Revisar contenidos manualmente** — Inspeccionar los cambios de esas rutas en orden histórico, sin imprimir valores, y asociar cada hallazgo con ref, commit y archivo.
4. **Actualizar el informe** — Documentar cobertura, ramas limpias dentro de este alcance, hallazgos y estado de rotación pendiente.
5. **Validar y publicar** — Comprobar que el diff contiene solo artefactos de auditoría, crear commit atómico y hacer push normal.

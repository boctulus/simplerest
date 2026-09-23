---
title: "SimpleRest: core mínimo y paquetes Composer"
current_step: 2
next_step: null
parallelizable_steps: []
parent: null
global_complexity: high
for_agents: false
next_step_complexity: null
tags: [composer, architecture, testing]
---
## Pasos planificados

1. **Inventariar límites actuales** — localizar capas del core, dependencias externas y componentes `packages/` para elegir extracciones independientes.
2. **Definir manifiestos Composer** — hacer que el framework y los componentes seleccionados tengan metadatos, autoload y requisitos compatibles con una instalación externa.
3. **Reducir el framework distribuido** — ajustar el empaquetador para excluir código de desarrollo y dejar un esqueleto de aplicación que arranque.
4. **Reparar pruebas reproducibles** — agrupar fallos independientes de servicios externos y arreglar contratos verificables de framework/Composer.
5. **Validar instalación limpia** — ejecutar Composer desde un directorio vacío, comprobar autoload y CLI, y dejar el push sujeto a higiene del historial.

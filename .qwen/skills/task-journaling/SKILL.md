---
name: task-journaling
description: Journaling efímero de la tarea activa en docs/pending/. Crear antes de ejecutar, actualizar paso a paso, eliminar al terminar.
---

# SKILL: task-journaling

## Propósito

Trazar la ejecución de UNA tarea activa mediante un archivo en `docs/pending/`. Se elimina al completar.

**No usar para backlog** → SKILL `todo-kanban`.

---

## Cuándo activar

Antes de ejecutar cualquier tarea no trivial.

---

## Archivo

```
docs/pending/<task-name>.md
```

`kebab-case`, sin espacios, corto.

---

## Estructura

```md
# <task-name>

ctx: <qué y por qué — 1-2 líneas>
src: prompts/<file>.md

## todo
- [ ] step-1
- [ ] step-2

## doing
- [ ] step-x

## done
- [x] step-y
```

`ctx` — ultra breve, sin narrativa.  
`src` — path al prompt, nunca el contenido.  
`todo` — pasos pendientes, atómicos, ordenados.  
`doing` — máximo 1–2 items.  
`done` — solo pasos completados.

---

## Protocolo

**Inicio:** crear archivo, poblar `ctx`, `src`, `todo`. `doing` y `done` vacíos.

**Durante:** mover cada paso `todo → doing → done`. No duplicar. Actualizar inmediatamente.

**Al terminar:** cuando `todo` y `doing` estén vacíos → eliminar el archivo.

---

## Extensión opcional

```md
## blockers
- descripción del bloqueo
```

---

## Prohibido

- Narrativa o logs extensos
- Código largo embebido
- Duplicar contenido del prompt
- Conservar el archivo después de terminar
- Usar `docs/pending/` para backlog o planificación

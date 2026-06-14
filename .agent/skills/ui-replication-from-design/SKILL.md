---
name: ui-replication-from-design
description: Reproduce UI interfaces from design files (Penpot/Figma/screenshot) adapting to the project's real technology stack — prioritizing visual fidelity, HTML semantics, responsiveness, and reusability
---

# 🧠 SKILL: `ui-replication-from-design`

## 🎯 Propósito

Reproducir una interfaz a partir de un diseño (Penpot / Figma / Screenshot) adaptándola **a las tecnologías reales del proyecto**, priorizando:

* fidelidad visual pragmática (no pixel-perfect rígido)
* semántica HTML
* responsividad
* reutilización
* bajo acoplamiento

---

# 🚨 PRECONDICIONES (VALIDACIÓN OBLIGATORIA)

Antes de comenzar, validar:

### Inputs requeridos

* Screenshot completo de la página (1 solo archivo)
* Export de Penpot / Figma (JSON o equivalente)

### Opcionales (pero recomendados)

* Assets reales (PNG / SVG)

---

## ⛔ Regla de bloqueo

Si falta alguno de los requeridos o el formato es incorrecto:

```txt
STOP
Notificar claramente qué falta y por qué no se puede continuar.
```

---

# 🧭 OBJETIVO OPERATIVO

El resultado debe cumplir:

* UI visualmente fiel al screenshot
* estructura semántica (HTML correcto)
* responsive real (no hacks)
* sistema reutilizable (no CSS ad-hoc)
* sin dependencia estructural del export de diseño

---

# 🧭 STACK TARGET

Debe detectarse dinámicamente según el proyecto.

## Caso: Svelte 5 + Tailwind CSS

* Svelte 5 (runes) → composición simple
* Tailwind → sistema de diseño
* Penpot/Figma → solo referencia (NO fuente estructural)

---

# 🧱 FASE 0 — RECONOCIMIENTO DEL PROYECTO

Antes de implementar:

1. Detectar:

   * framework
   * sistema de estilos
   * estructura existente

2. Ajustar la estrategia al stack real

---

# 🧱 FASE 1 — ANÁLISIS DEL SCREENSHOT (FUENTE PRINCIPAL)

## 1. Segmentación por secciones

Dividir verticalmente:

```txt
[header]
[hero]
[features]
[testimonials]
[pricing]
[cta]
[footer]
```

### Regla

> Cada cambio visual significativo = nueva sección

---

## 2. Análisis de layout por sección

Para cada sección:

```txt
- tipo: stack | grid | 2 columnas | centrado
- distribución interna
```

Ejemplo:

```txt
hero:
  layout: 2 columnas
  left: texto + CTA
  right: imagen
```

---

## 3. Identificación de patrones

Detectar elementos repetidos:

* botones
* cards
* icon + texto
* listas

Definir:

```txt
button-primary
feature-card
section-title
```

### Regla crítica

> No duplicar estructuras visuales iguales

---

## 4. Spacing aproximado (pre-tokenización)

```txt
section spacing: grande (~80px)
element spacing: medio (~24px)
inner spacing: pequeño (~12px)
```

---

# 🧩 FASE 2 — EXTRACCIÓN DESDE PENPOT/Figma

## 5. Lectura estructural mínima

Usar:

* manifest.json
* files/*.json

Solo para ubicar:

* frame raíz
* secciones principales

---

## 6. Extracción de TOKENS (NO layout)

### 🎨 Colores

```json
"fill": "#XXXXXX"
```

→

```js
colors = {
  primary,
  secondary,
  text,
  background
}
```

---

### 🔤 Tipografía

```json
fontSize
fontFamily
fontWeight
```

→

```js
typography = {
  h1,
  h2,
  body
}
```

---

### 📏 Spacing real

Detectar valores repetidos:

```js
spacing = {
  xs: 8,
  sm: 16,
  md: 24,
  lg: 32,
  xl: 64
}
```

---

## ⛔ Regla crítica

Prohibido:

* usar coordenadas absolutas
* replicar posiciones exactas
* construir layout desde JSON

> Solo se extrae sistema, no estructura

---

# 🧱 FASE 3 — RESPONSIVE

## 7. Definición de breakpoints

```js
breakpoints = {
  mobile: 0,
  tablet: 768,
  desktop: 1024
}
```

---

## 8. Adaptación por sección

Ejemplo:

```txt
hero:
  desktop: 2 columnas
  mobile: 1 columna (imagen abajo)
```

---

## 9. Reglas globales

* stack vertical en mobile
* padding lateral consistente
* uso de max-width
* evitar overflow horizontal

---

# 🧱 FASE 4 — IMPLEMENTACIÓN

## 10. Estructura base

```html
<section class="hero">
  <div class="container">
    ...
  </div>
</section>
```

---

## 11. Definir sistema ANTES de maquetar

* container (max-width + padding)
* spacing scale
* font scale
* color palette

---

## 12. Orden de implementación

1. Header
2. Hero
3. Secciones intermedias
4. Footer

### Regla

> Nunca implementar toda la página simultáneamente

---

## 13. Uso de assets

* usar solo los necesarios
* optimizar peso
* preferir SVG si aplica
* evitar PNG gigantes

---

# ⚠️ FASE 5 — CONTROL DE CALIDAD

## 14. Checklist por sección

* fidelidad visual ✔
* responsive ✔
* reutilización ✔
* consistencia de spacing ✔

---

## 15. Red flags

Evitar:

```txt
position: absolute masivo
margins arbitrarios
valores hardcode inconsistentes
pixel-perfect desde JSON
```

---

# ❌ ERRORES ARQUITECTÓNICOS

## 1. Stores innecesarios

* estado local → componente

---

## 2. Componentización prematura

* solo extraer cuando hay repetición real

---

## 3. Violación del spacing system

```html
class="mt-[23px]" ❌
```

---

## 4. Uso directo de JSON de Penpot

* no aplica para layout en este stack

---

# 🧠 DECISIÓN ARQUITECTÓNICA

## ❌ Enfoque incorrecto

> Convertir Penpot → componentes automáticamente

Problemas:

* rompe estándares
* genera ruido
* pierde control visual

---

## ✔ Enfoque correcto

```txt
Tailwind = sistema
Svelte = composición
Penpot = referencia
```

---

# 🧠 MODELO MENTAL

## Fuente de verdad

| Fuente      | Uso                          |
| ----------- | ---------------------------- |
| Screenshot  | layout y visual              |
| Penpot JSON | tokens (colores, fonts, etc) |
| Assets      | imágenes                     |

---

# 🧠 REGLA DE ORO

> El diseño se interpreta, no se transpila.



---
name: code-naming-conventions-contract
description: Naming Convention & Dependency Protection
---

# SKILL_DEFINITION: naming convention

# SKILL: Naming Convention & Dependency Protection

**Purpose**  
Establecer reglas estrictas y no negociables de nomenclatura y protección de dependencias para evitar bugs sutiles, deuda técnica y compatibilidad implícita entre capas.

---

## Scope

Aplica a **todo el código** del proyecto:

* Backend / Frontend
* Base de datos
* Vistas (HTML/EJS)
* CSS / JS
* APIs y contratos de datos
* **Sistema de archivos (nombres de archivos y carpetas)**

---

## Naming Rules (Obligatorias)

### 📁 Archivos (CRÍTICO — SIN EXCEPCIONES)

* **Todos los archivos deben usar `kebab-case`**

```bash
sales-session-manager.js
http-interceptor.js
cart-persistence.js
product-search.js
````

❌ No permitido:

```bash
SalesCartAdapter.js     // PascalCase
salesSessionManager.js  // camelCase
sales_session.js        // snake_case
```

**Razón:**

* Consistencia con ecosistema Node.js y frontend moderno
* Evita problemas en sistemas case-sensitive
* Mejora legibilidad en rutas y paths
* El nombre del archivo representa el **rol del módulo**, no su implementación interna

### 🚨 Creación de Archivos (LLM Enforcement)

Si una tarea implica:

* Crear nuevos archivos
* Sugerir nombres de archivos
* Generar estructura de proyecto

Entonces:

> ✅ **Los nombres deben ser convertidos automáticamente a `kebab-case`, incluso si el prompt usa otro formato.**

---

### Ejemplo

**Input del prompt:**

```text
Create a file named SalesCartAdapter.js
```

**Salida obligatoria:**

```bash
sales-cart-adapter.js
```

---

### 📁 Carpetas

* **kebab-case obligatorio**

```bash
cashbox-module/
sales-engine/
```

---

### Código

* **Classes:** `PascalCase`
* **Variables / Functions / Methods:** `camelCase`
* **Constants:** `SCREAMING_SNAKE_CASE` (si aplica)

---

### Persistencia (NO EXCEPCIONES)

* **Tables:** `snake_case`
* **Collections:** `snake_case`
* **Database Fields:** `snake_case`

> ⚠️ No se permiten variantes ni compatibilidad cruzada de casing.

---

## Relationship Between File Name and Code (IMPORTANTE)

* El archivo **NO debe reflejar el nombre exacto de la clase**, sino su responsabilidad.

```js
// ✔ Correcto
// archivo: sales-cart-adapter.js
export class SalesCartAdapter {}
```

```js
// ❌ Incorrecto
// archivo: SalesCartAdapter.js
export class SalesCartAdapter {}
```

---

## Forbidden Patterns 🚫

### Fallbacks de casing

No se deben implementar soluciones que acepten múltiples convenciones para “convivir” con errores previos.

```js
// ❌ MAL
const userStoreId = user.store_id || user.storeId;
```

---

### Mapeos tolerantes o correctivos

No se deben crear capas que intenten corregir o mapear casing incorrecto.

```js
// ❌ MAL
async _mapFieldsToSchema(context) {
  const data = context.data;

  const fieldMapping = {
    'ticketNumber': 'ticket_number',
    'ticketnumber': 'ticket_number',
    'cashboxSessionId': 'cashbox_session_id',
    'cashboxsessionid': 'cashbox_session_id',
  };
}
```

**Razón:**

* Oculta errores reales
* Aumenta el contexto cognitivo
* Rompe contratos implícitos
* Genera dependencias invisibles

---

## Contract-First Principle

* El **casing correcto es parte del contrato**
* Si un input llega mal nombrado → **es un bug**
* Los errores deben fallar rápido y explícitamente

---

## Dependency Protection (Crítico)

### Regla general

No cambies identificadores que puedan ser dependencias externas
**a menos que el objetivo explícito del cambio sea ese**

---

### Identificadores protegidos

* Campos de base de datos
* IDs (cualquier tipo)
* Clases o IDs HTML usados como selectores
* Selectores CSS
* Hooks de JavaScript
* Nombres de archivos referenciados dinámicamente

---

### HTML

* Mantener **intactos** los `data-* attributes`

```html
<!-- ✔ Correcto -->
<button data-ticket-id="123">Confirm</button>
```

---

## Allowed Changes ✔

* Refactors internos **sin romper contratos**
* Mejoras de legibilidad respetando casing
* Cambios de naming **solo si**:

  * Se actualizan todas las dependencias
  * Se documenta explícitamente el breaking change

---

## Enforcement

* Pull Requests que violen este SKILL deben ser rechazados
* No se aceptan excepciones “temporales”
* No se introduce deuda técnica deliberada
* Los LLMs deben seguir esta regla sin inferencias ni interpretaciones

---

## TL;DR

* Archivos y carpetas → **kebab-case obligatorio**
* Clases → PascalCase
* Variables → camelCase
* DB → snake_case
* El casing **no se negocia**
* No hay fallbacks
* No hay mapeos tolerantes
* Los contratos se respetan
* Los errores se corrigen en el origen


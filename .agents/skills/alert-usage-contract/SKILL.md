---
name: alert-usage-contract
description: UI Alerts Usage
---

# SKILL: UI Alerts Usage

## Propósito

Definir reglas estrictas y determinísticas para el uso de alertas en la UI, asegurando consistencia, evitando abuso visual y mejorando la experiencia del usuario.

Este SKILL decide **qué tipo de alerta usar en función del contexto, resultado y necesidad de interacción**.

---

## Inputs esperados

* `result`: `"success" | "error" | "warning" | "info"`
* `requiresUserAction`: `boolean`
* `isCritical`: `boolean`
* `isFromBackendRedirect`: `boolean`
* `isSimpleConfirmation`: `boolean`

---

## Salida esperada

* `alertMethod`: `"toastr.success" | "showAlert" | "btModal" | "flashNotification"`
* `justification`: string breve explicando la decisión

---

## Reglas principales (orden de prioridad)

### 1. ❌ PROHIBICIÓN ABSOLUTA

* NUNCA usar `alert()` nativo de JavaScript
* NUNCA usar SweetAlert directamente (usar solo `showAlert()`)
* NUNCA mostrar errores con flashNotification() cuando son de validacion en el frontend.

Además,...

* NUNCA cerrar automáticamente una ventana modal cuando contiene errores de validación. El usuario debe corregirlos explícitamente.

---

### 2. ✅ CASO: Resultado exitoso

```pseudo
IF result == "success" THEN
    USE toastr.success
```

**Regla fuerte:**

* `toastr.success` es la ÚNICA opción válida para resultados exitosos
* No evaluar ninguna otra condición

---

### 3. ❌ CASO: Resultado NO exitoso

```pseudo
IF result != "success" THEN
    USE showAlert
```

**Incluye:**

* errores
* warnings importantes
* validaciones críticas

**Regla fuerte:**

* `showAlert()` es la ÚNICA opción válida para resultados negativos
* No usar toastr en estos casos

---

### 4. ⚠️ CASO: Confirmaciones del usuario

```pseudo
IF requiresUserAction == true THEN
    IF isSimpleConfirmation == true THEN
        USE btModal
    ELSE
        USE showAlert
```

**Ejemplos:**

* Confirmar eliminación → `btModal`
* Decisiones complejas → `showAlert`

---

### 5. 🔁 CASO: Eventos desde backend (redirect)

```pseudo
IF isFromBackendRedirect == true THEN
    USE flashNotification
```

**Restricción fuerte:**

* Solo usar si el mensaje proviene de otro módulo tras redirect
* Uso excepcional y explícito

---

## Reglas de exclusión (muy importantes)

### Toastr

**Permitido SOLO si:**

* `result == "success"`

**Prohibido para:**

* errores
* warnings
* confirmaciones

---

### flashNotification

**Uso restringido:**

* Solo backend + redirect

**Prohibido para:**

* flujos activos
* interacción del usuario
* notificaciones locales del módulo

---

### showAlert

**Usar para:**

* errores
* warnings críticos
* validaciones importantes
* decisiones complejas

**Prohibido para:**

* operaciones exitosas
* confirmaciones simples

---

### btModal

**Usar solo para:**

* confirmaciones explícitas del usuario

**Prohibido para:**

* notificaciones
* mensajes automáticos

---

## Tabla de decisión simplificada

| Condición principal             | Resultado           |
| ------------------------------- | ------------------- |
| `result == success`             | `toastr.success`    |
| `result != success`             | `showAlert`         |
| `requiresUserAction && simple`  | `btModal`           |
| `requiresUserAction && !simple` | `showAlert`         |
| `isFromBackendRedirect`         | `flashNotification` |

---

## Orden de evaluación recomendado

```pseudo
1. IF isFromBackendRedirect → flashNotification
2. ELSE IF requiresUserAction → btModal o showAlert
3. ELSE IF result == success → toastr.success
4. ELSE → showAlert
```

---

## Ejemplos canónicos

### ✅ Guardado exitoso

```json
{
  "result": "success",
  "requiresUserAction": false
}
```

→ `toastr.success`

---

### ❌ Error en formulario

```json
{
  "result": "error",
  "requiresUserAction": false
}
```

→ `showAlert`

---

### ⚠️ Confirmar eliminación

```json
{
  "requiresUserAction": true,
  "isSimpleConfirmation": true
}
```

→ `btModal`

---

### 🔥 Advertencia crítica

```json
{
  "result": "warning",
  "isCritical": true
}
```

→ `showAlert`

---

### 🔁 Mensaje desde backend (redirect)

```json
{
  "isFromBackendRedirect": true
}
```

→ `flashNotification`

---

## Anti-patrones

* Usar `toastr.error` → ❌ PROHIBIDO
* Usar `toastr.warning` → ❌ PROHIBIDO
* Usar `showAlert` para éxito → ❌ PROHIBIDO
* Usar `btModal` sin interacción → ❌ PROHIBIDO
* Usar `flashNotification` por defecto → ❌ PROHIBIDO

---

## Principio UX

> A mayor impacto o riesgo, mayor visibilidad y bloqueo de la alerta.

---

## Archivos relacionados

* `/assets/js/alerts.js`
* `/views/layouts/*.ejs`
* `/js/componentLoader.js`



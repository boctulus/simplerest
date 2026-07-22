---
name: push-notifications-architecture
description: Strategy Selection for Push Notifications Systems (ONLY for greenfield or full redesign)
---

# SKILL: Push Notifications Architecture & Channel Strategy

# ACTIVATION RULES (STRICT — NON NEGOTIABLE)

Este SKILL **SOLO puede activarse si se cumple al menos UNA de estas condiciones**:

1. El usuario solicita explícitamente:
   - “desde cero”
   - “arquitectura”
   - “diseñar sistema”
   - “qué tecnología usar”

2. El sistema:
   - NO existe aún (greenfield)
   - o será completamente reemplazado

---

## 🚫 NO ACTIVAR si:

- El usuario ya tiene un sistema funcionando
- La consulta es sobre:
  - bugs
  - mejoras incrementales
  - optimización
  - integración puntual
- El usuario pide:
  - “cómo arreglar X”
  - “por qué no funciona Y”
  - “cómo mejorar esto existente”

👉 En estos casos:
**responder directamente sin invocar este SKILL**

---

# PURPOSE

Definir una estrategia correcta para sistemas de notificaciones
**solo en fase de diseño inicial o rediseño completo**

---

# CORE PRINCIPLE

No existe un único canal que cubra todos los casos.

Arquitectura requerida:
> multi-canal + contexto-dependiente + degradación progresiva

---

# DECISION FRAMEWORK

## 1. Clasificar evento

- Tiempo real crítico → WebSocket
- Informativo → FCM/Web Push
- Reactivación → FCM/Web Push

---

## 2. Selección de canal

| Tipo | Canal principal | Secundario |
|------|----------------|-----------|
| Tiempo real | WebSocket | FCM (opcional) |
| Informativo | FCM/Web Push | — |
| Reactivación | FCM/Web Push | — |

---

# ARCHITECTURE PATTERN

Hybrid model obligatorio:

- WebSocket → clientes activos
- FCM/Web Push → clientes inactivos

---

# NON-NEGOTIABLE RULES

1. No usar solo FCM para tiempo real
2. No usar push como fuente de verdad
3. Siempre sincronizar estado al reconectar
4. Diseñar idempotencia
5. Asumir desconexión como caso normal

---

# OUTPUT CONSTRAINT

Este SKILL:

✔️ Define arquitectura  
✔️ Define decisiones de canal  

❌ NO debe:
- reescribir sistemas existentes
- proponer migraciones no solicitadas
- introducir cambios si el usuario no lo pide

---

# SUMMARY

Uso válido:
→ diseño inicial o rediseño completo

Uso inválido:
→ sistemas ya en producción o en funcionamiento


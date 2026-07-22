---
name: cross-layer-naming-consistency-contract
description: Enforce unified naming across all layers and prevent semantic drift
---

# SKILL_DEFINITION: Cross-Layer Naming Consistency

## Purpose

Ensure that **all identifiers representing the same concept use the exact same name across all layers**, preventing silent data corruption and contract mismatches.

---

## 🔴 Core Rule (NON-NEGOTIABLE)

> A domain concept MUST have ONE canonical name across the entire system.

This includes:

- Frontend (views, JS, state)
- API payloads (request/response)
- Backend (services, DTOs, models)
- Database fields

---

## 🌐 Language Rule (CRITICAL)

> ✅ ALL identifiers MUST be in ENGLISH

Applies to:

- Variables
- JSON fields
- API payloads
- Database columns
- HTML attributes (except visible text)

---

### ❌ Forbidden

```js
item.precio
item.cantidad
item.codigo
````

### ✅ Required

```js
item.price
item.quantity
item.productId
```

---

## 🔍 Mandatory Naming Discovery Phase (BEFORE CODING)

Before introducing ANY new identifier, the system MUST:

1. Search existing codebase for similar concepts
2. Identify canonical names already in use
3. Reuse them EXACTLY

---

### ❌ Forbidden

Creating new synonyms:

```js
price → amount → cost → value
```

---

### ✅ Required

```js
// If "price" exists → ALWAYS reuse "price"
```

---

## 🔗 Cross-Layer Contract Enforcement

If a field exists:

```json
{
  "price": 100
}
```

Then it MUST be:

| Layer    | Name  |
| -------- | ----- |
| Frontend | price |
| API      | price |
| Backend  | price |
| Database | price |

---

## 🚫 Forbidden Patterns

### 1. Semantic aliases

```js
// ❌
price !== amount !== cost
```

---

### 2. Layer-based renaming

```js
// ❌ frontend
item.price

// ❌ backend
item.unit_price
```

---

### 3. Translation layers

```js
// ❌ mapping layer
precio → price
cantidad → quantity
```

---

## 💣 Failure Mode (Important)

If inconsistent naming is detected:

> 🚨 MUST STOP and FIX THE SOURCE

Not:

* mapping
* fallback
* patching

---

## 🔄 Refactoring Rule

If inconsistency exists:

1. Choose canonical name (English)
2. Refactor ALL layers
3. Update ALL dependencies
4. No coexistence allowed

---

## 🧪 Validation Checklist

Before accepting code:

* [ ] Same concept → same name everywhere
* [ ] No Spanish identifiers
* [ ] No synonyms
* [ ] No mapping layers
* [ ] No fallback logic

---

## Guiding Principle

> “If two names exist for the same concept, one of them is a bug.”

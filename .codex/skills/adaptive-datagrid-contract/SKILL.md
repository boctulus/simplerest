---
name: adaptive-datagrid-contract
description: Strict contract for search-filter-datagrid. Enforces mobile-first, column limits, and action consistency.
---

# SKILL_DEFINITION: adaptive-datagrid-contract

Strict configuration contract for `search-filter-datagrid`.

⚠️ This is NOT a helper. This is a REQUIRED contract.

---

# 🔒 HARD RULES (NON-NEGOTIABLE)

## 1. NEVER create custom tables
- Do NOT use `<table>` manually
- Do NOT use external datagrid libraries
- ALWAYS use `search-filter-datagrid`

---

## 2. MAX columns rule (CRITICAL)

- Default: **max 5 columns**
- Absolute max: **7 columns**
- If more data is needed → use `computed` or secondary views

❌ WRONG:
```js
columns: [id, name, email, phone, address, city, country, created_at]
````

✅ CORRECT:

```js
columns: [name, email, phone, status, __actions]
```

---

## 3. MOBILE-FIRST ENFORCEMENT

Columns MUST follow priority:

| Priority | Meaning                             |
| -------- | ----------------------------------- |
| 1        | Critical (always visible)           |
| 2        | Important                           |
| 3        | Optional (can be dropped in mobile) |

Example:

```js
{ key: "name", priority: 1 },
{ key: "email", priority: 2 },
{ key: "created_at", priority: 3 }
```

If priority is not defined:
→ assume priority = 2

---

## 4. ACTION COLUMN (MANDATORY RULES)

If actions exist:

```js
{
  "label": "Acciones",
  "key": "__actions",
  "align": "right",
  "tdClass": "text-end",
  "width": "180px"
}
```

Rules:

* ALWAYS right-aligned
* ALWAYS fixed width
* NEVER omit `__actions` if buttons exist

---

## 5. FORBIDDEN PATTERNS

### ❌ cellRenderers (NOT SUPPORTED)

### ❌ Complex HTML inside `computed`

### ❌ More than 2 nested elements in `computed`

### ❌ Inline styles

### ❌ Business logic inside `formatter` or `computed`

---

## 6. COMPUTED USAGE RULE

Use ONLY for:

* badges
* simple links
* short text transformations

❌ BAD:

```js
computed: (row) => `<div><div><div>complex UI</div></div></div>`
```

✅ GOOD:

```js
computed: (row) => `<span class="badge bg-success">Active</span>`
```

---

## 7. FORMATTER RULE

Use `formatter` ONLY for:

* numbers
* currency
* dates
* simple string formatting

---

## 8. DEFAULT SAFE TEMPLATE

Always start from this:

```js
const columns = [
  { key: "name", priority: 1 },
  { key: "email", priority: 2 },
  {
    key: "status",
    priority: 2,
    computed: (row) =>
      row.active
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-danger">Inactive</span>'
  },
  {
    key: "__actions",
    align: "right",
    tdClass: "text-end",
    width: "180px"
  }
];
```

---

## 9. DECISION RULES

When generating columns:

1. Prefer **readability over completeness**
2. Prefer **fewer columns**
3. Prefer **computed over extra columns**
4. Always include `__actions` if buttons exist

---

## 10. FAILURE CONDITIONS

The implementation is INVALID if:

* More than 7 columns
* Missing `__actions` with buttons
* Uses unsupported properties
* Breaks mobile readability

---

## REQUIRES (HARD DEPENDENCIES)

These SKILLs MUST be active before this SKILL can execute:

- view-standards-contract
- component-creation-contract

If any are missing:
→ STOP
→ LOAD them
→ RESTART execution

## SKILL ORDER EXECUTION (ENFORCED SEQUENCE)

Apply dependencies in this exact order:

1. view-standards-contract
2. component-creation-contract

## TRIGGERS

### ON_COMPLETE

→ APPLY SKILL: grid-integrity-guard--bootstrap-flexbox

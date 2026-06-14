---
name: related-table-filter
description: Enable filtering by related table fields through $connect_to and dot-notation.
---

# SKILL_DEFINITION: related-table-filter

## ACTIVATION (ENTRY GATE)

This SKILL MUST only be applied when ALL of the following conditions are true:

- The user asks how to filter an auto-endpoint by a field that lives in a **related/pivot table** (e.g. `role` column in `roles` but not in `users`)
- The framework is SimpleRest
- The solution involves set `$connect_to` to the API controller.

---

If these conditions are NOT met:

-> DO NOT APPLY this SKILL
-> STOP reading further instructions
-> Continue with other relevant SKILLs

## EXECUTION PLAN (MANDATORY)

STEP 1: Identify the table relationship chain

TYPE: ACTION

ACTION: Read the schema files for the main table and the related table. Verify the relationship exists in `app/Schemas/main/{Table}Schema.php` under `relationships` or `expanded_relationships`. Check `app/Schemas/main/Relations.php` for N:M pivot resolution.

ON_FAILURE:
-> STOP
-> REPORT ERROR: Relationship not found between tables

---

STEP 2: Choose the filter approach

Dot-notation via $connect_to (simplest, no custom code)
    - URL: `?related_table.field=value`
    - Requires adding the table to `static protected $connect_to` in the controller

ON_FAILURE:
-> STOP
-> REPORT ERROR: Could not determine the approach

---

STEP 3: Implement in the controller

TYPE: ACTION

ACTION: Edit `app/Controllers/Api/{Resource}.php`:

  For approach A (dot-notation only):
  ```php
  static protected $connect_to = [
      'related_table'
  ];
  ```
URL: `GET /api/v1/resource?related_table.field=value`

ON_FAILURE:
-> STOP
-> REPORT ERROR: Controller modification failed

---

## REQUIRES (HARD DEPENDENCIES)

These SKILLs MUST be active before this SKILL can execute:

- automatic-endpoints
- subresources
- query-builder

If any are missing:
-> STOP
-> LOAD them
-> RESTART execution

## SKILL ORDER EXECUTION (ENFORCED SEQUENCE)

Apply dependencies in this exact order:

1. automatic-endpoints
2. subresources
3. query-builder

## TRIGGERS

### ON_CONDITION

IF user asks about filtering by related table field (role, category name, etc.) on an auto-endpoint
-> APPLY SKILL: related-table-filter

---

### ON_COMPLETE

-> APPLY SKILL: automatic-endpoints

## Overview

### The Problem

When a field exists in a **related table** (not the main table), the auto-endpoint returns `400 Unknown field` because the field isn't in the schema's `attr_types`. Example: `users` has no `role` column; roles are in `roles` via the pivot `user_roles`.

### Two Framework Mechanisms

**1. Dot-notation + `$connect_to`** (in `ApiController.php`)

The `get()` method in `ApiController` detects filters containing a dot (`user_roles.role_id`). It:
1. Checks the table prefix is in `static::$connect_to`
2. Validates the field exists in the referenced table's schema
3. Creates a JOIN using the schema relationship
4. Rewrites the filter to the aliased table

```
GET /api/v1/users?user_roles.role_id=3
```

**2. Lifecycle hooks** 

In the case of needing custom logic (e.g. filter by `roles.name` instead of `user_roles.role_id`), you can use the `onGettingAfterCheck` hook in the controller. This runs after the main query is built but before it executes, allowing you to inject custom joins and where clauses.

Override this method to inject custom query logic before the main query runs:

```php
protected function onGettingAfterCheck($id)
{
    if ($id !== null) return;
    $role = request()->shiftQuery('role');
    if ($role === null) return;
    // Custom join + where logic
}
```

### N:M Auto-Join Resolution

For N:M relationships (like `users` <-> `roles` via `user_roles`), the `join('roles')` call in QueryBuilderTrait (lines 685-717) automatically resolves the pivot:

```
->join('roles')  generates:
  INNER JOIN user_roles ON users.id = user_roles.user_id
  INNER JOIN roles ON roles.id = user_roles.role_id
```

Do NOT need to call `join('user_roles')` before `join('roles')` in the name-based path — the N:M resolution already handles it.

### URL Examples

| URL | Approach | What it does |
|-----|----------|-------------|
| `GET /api/v1/users?user_roles.role_id=3` | A | Filter by role ID (numeric value in pivot) |
| `GET /api/v1/users?role=co_owner` | B | Filter by role name (hook reads `role` param, joins roles) |
| `GET /api/v1/users?role=3` | B | Filter by role ID (hook detects numeric, joins user_roles only) |

## Key Files

| File | Role |
|------|------|
| `app/Controllers/Api/Users.php` | Controller — add `$connect_to` and hooks here |
| `app/Schemas/main/UsersSchema.php` | Schema — defines `relationships` (user_roles pivot) |
| `app/Schemas/main/UserRolesSchema.php` | Pivot schema — links users <-> roles |
| `app/Schemas/main/RolesSchema.php` | Target schema — has the `name` field |
| `app/Schemas/main/Relations.php` | Computed relations — declares `users~roles` as N:M |
| `src/framework/Api/ApiController.php` | Core — `get()` at line 295, hook at line 2481, dot-notation at line 810 |
| `src/framework/Traits/QueryBuilderTrait.php` | Core — `join()` auto-resolution at line 632 |

## Known Pitfalls

1. **Duplicate joins**: If you call `join('user_roles')` and then `join('roles')`, the N:M resolution of `join('roles')` will also join `user_roles`. Fix: only join the target table when using N:M resolution.
2. **`qualify()` interference**: After the hook runs, the auto-endpoint may call `$this->instance->qualify()`. Use fully qualified field names (`roles.name` not `name`) in your `where()` calls.
3. **`shiftQuery` is single-use**: It removes the key from the internal `$query_arr`. Multiple calls for the same key return the cached value from a static array.
4. **Hook skip on single-record**: Always check `if ($id !== null) return;` — the hook fires for both list and single-record GETs. Role filtering only makes sense on lists.

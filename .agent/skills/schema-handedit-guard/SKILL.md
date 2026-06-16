---
name: schema-handedit-guard
description: Prevents hand-editing auto-generated schema files in app/Schemas/. Forces regeneration from database via php com make schema instead.
---

# SKILL_DEFINITION: Schema Hand-Edit Guard

## ACTIVATION (ENTRY GATE)

This SKILL MUST only be applied when ALL of the following conditions are true:

- The task involves editing, creating, or modifying a file matching `app/Schemas/*/*Schema.php`
- The file is NOT being created by running `php com make schema`

---

If these conditions are NOT met:

→ DO NOT APPLY this SKILL
→ STOP reading further instructions
→ Continue with other relevant SKILLs

## EXECUTION PLAN (MANDATORY)

STEP 1: Block hand-edit of schema file

TYPE: CHECK

CHECK:
- Target path matches `app/Schemas/{connection}/*Schema.php`
- Operation is a direct file edit (not running `php com make schema`)

ON_FAILURE:
→ STOP
→ REPORT ERROR: Schema files are auto-generated from the database. Hand-editing is forbidden because `php com make schema all` will overwrite them.

STEP 2: Redirect to generator

TYPE: ACTION

ACTION: Tell the user to use `php com make schema {table} --force` to regenerate a single schema, or `php com make schema all` to regenerate all schemas from the database. If a column needs to be added, first add it to the database table via a migration, then regenerate.

ON_FAILURE:
→ STOP
→ REPORT ERROR: Could not redirect to proper schema generation command.

## REQUIRES (HARD DEPENDENCIES)

These SKILLs MUST be active before this SKILL can execute:

- anti-hallucination-project-guard

If any are missing:
→ STOP
→ LOAD them
→ RESTART execution

## SKILL ORDER EXECUTION (ENFORCED SEQUENCE)

Apply dependencies in this exact order:

1. anti-hallucination-project-guard

## TRIGGERS

### ON_COMPLETE

→ APPLY SKILL: schemas

## NOTES

- ALL files matching `app/Schemas/*/*Schema.php` are auto-generated from database tables using `php com make schema`.
- `Relations.php` and `Pivots.php` in the same directory are also auto-generated.
- NEVER hand-edit these files. If you need to add a column or relationship, create a migration to alter the database table, then regenerate.
- Exception: If the user explicitly says they understand the file will be overwritten and still wants to edit it temporarily, you may proceed — but warn them first.

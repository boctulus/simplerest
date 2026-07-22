---
name: incident-docs-protocol
description: Use when documenting a production bug, incident, or error. Separates incident-specific context from reusable knowledge.
---

# SKILL_DEFINITION: Dual Layer Documentation

Forces strict separation between:

- Contextual (non-portable) information
- Reusable (portable) knowledge

---

# 1. Core Principle

Documentation MUST NEVER mix:

- Real-world incident data
- Reusable solutions

---

# 2. Layer Definitions

## 2.1 Incident Layer

Used for:

- Production bugs
- Logs
- Real errors

Allowed:

- Real paths
- Real names
- Exact logs

Requirements:

- Must be clearly labeled
- Must NOT contain reusable code

---

## 2.2 Knowledge Layer

Used for:

- Fixes
- Patterns
- Architecture

Must follow:

- context-sanitizer-contract (STRICT)

---

# 3. Mandatory Structure

All docs MUST follow:

````

## Incident (Contextual)

## Root Cause (Abstracted)

## Reusable Fix (Sanitized)

## Portable Validation

````

---

# 4. Hard Rules

## 4.1 No Cross-Contamination

## 4.2 Required Abstraction

### Backend

| From              | To                  |
| ----------------- | ------------------- |
| Real path         | `appPath()`         |
| Real project name | generic placeholder |
| Real command      | portable command    |
| Hardcoded port    | `process.env.PORT`  |

---

# 5. Validation Rules

Before output:

* Are layers separated?
* Is reusable code sanitized?
* Is incident clearly marked?

If not → rewrite.

---

# 6. Failure Modes

## 6.1 Mixed Layers

Most common error → MUST fix

---

## 6.2 Over-Sanitization

Do NOT remove useful debugging info from incident layer

---

# 7. Priority

Runs BEFORE:

* context-sanitizer-contract

Overrides:

* docs-authoring-protocol

---

# 8. Integration Rule

If both layers exist:

* Apply sanitizer ONLY to knowledge layer
* NEVER alter incident layer

---

## REQUIRES (HARD DEPENDENCIES)

These SKILLs MUST be active before this SKILL can execute:

- context-sanitizer-contract

If any are missing:
→ STOP
→ LOAD them
→ RESTART execution

## SKILL ORDER EXECUTION (ENFORCED SEQUENCE)

Apply dependencies in this exact order:

1. context-sanitizer-contract

## TRIGGERS

### ON_COMPLETE

→ APPLY SKILL: docs-authoring-protocol
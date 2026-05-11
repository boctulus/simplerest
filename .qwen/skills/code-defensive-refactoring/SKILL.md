---
name: code-defensive-refactoring
description: Densive Refactoring Discipline
---

# SKILL_DEFINITION: defensive-refactoring

## Purpose

Establish a **defensive refactoring discipline** that minimizes risk, prevents unintended semantic drift, and ensures recoverability by enforcing **automatic backups**, **phased changes**, and **context integrity validation** before, during, and after refactoring operations.

This skill is mandatory for any refactoring that modifies:

* Existing production code
* Shared modules or libraries
* Public APIs or data contracts
* Business-critical logic

---

## Core Principles

### 1. Refactoring Must Be Reversible

Every refactoring action **must be recoverable without external tooling**.

* Before modifying any file, create a **byte-level copy** of the original file.
* Backups must be created **automatically**, not manually.
* No refactoring step is considered valid without a backup.

---

### 2. Mandatory Pre-Modification Backup

Before any modification:

* Copy each target file to a **temporary absolute path**, e.g.:

```
/tmp/refactor-backups/{timestamp}/{relative_original_path}
```

Example:

```
/tmp/refactor-backups/2026-02-16-1032/app/Modules/Oders/Models/Order.php
```

Rules:

* The backup **must preserve directory structure**
* The backup **must be immutable** during the refactoring process
* The backup **represents the semantic baseline**

---

### 3. Phased (Staged) Refactoring Only

Refactoring must be executed in **explicit phases**, never in a single broad change.

#### Recommended phases:

1. **Preparation phase**

   * Backup creation
   * Static analysis
   * Identify refactoring scope

2. **Structural phase**

   * File moves
   * Function extraction
   * Renaming without logic changes

3. **Behavioral phase**

   * Logic improvements
   * Performance changes
   * Algorithm replacement

4. **Verification phase**

   * Context integrity checks
   * Tests comparison
   * Diff analysis vs backup

Each phase must:

* Compile independently
* Preserve runtime behavior unless explicitly intended

---

### 4. Context Integrity Enforcement

Refactoring **must not introduce unintended context shifting**.

The following elements **must be validated against the backup version**:

* Field names
* Attribute names
* Public method signatures
* DTO / schema properties
* Configuration keys
* Business-critical constants

#### Forbidden patterns:

* Silent renaming of fields without migration logic
* Changing semantic meaning under the same identifier
* Introducing domain concepts not present in the original context
* Mixing responsibilities during refactoring

If a name change is intentional:

* It must be explicitly documented
* It must be traceable to a business or architectural decision

---

### 5. Semantic Diff Over Visual Diff

Visual diffs are insufficient.

Refactoring validation must focus on:

* **Meaning preservation**
* **Contract stability**
* **Behavioral equivalence**

The refactored version must be explainable as:

> “The same system, expressed more clearly or safely.”

If that statement cannot be defended, the refactoring is invalid.

---

### 6. Incremental Commit Discipline

Each refactoring phase must result in:

* A minimal, focused change set
* A commit that can be reverted independently
* A clear and narrow commit message

No “cleanup + logic + renaming” in a single step.

---

### 7. Refactoring Is Not Redesign

This skill explicitly separates:

* **Refactoring** → improving structure without changing behavior
* **Redesign** → changing behavior or domain rules

If redesign is needed:

* Stop refactoring
* Create a new task or phase
* Re-establish backups

---

### 8. Post-Refactor Verification Checklist

Before considering refactoring complete:

* Backups exist and are intact
* No critical identifiers changed unintentionally
* Public contracts remain stable
* Tests pass without semantic modification
* Code reviewers can map old → new logic mentally

---

## Failure Conditions

A refactoring **must be rejected** if:

* No backup exists
* Changes cannot be reversed safely
* Context drift is detected
* The scope exceeds the declared phase
* Behavior changes are not explicitly justified

---

## Guiding Principle

> “Refactoring is successful when the code changes,
> but the system’s meaning does not.”


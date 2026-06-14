---
name: post-task-verification
description: Enforce post-task validation including change tracking, intent alignment, encoding integrity, and regression detection
---
```

# POST TASK VERIFICATION

## Objective

Ensure that every completed task:

1. Matches the original intent
2. Only modifies expected files and components
3. Does not introduce encoding or formatting issues
4. Does not create unintended side effects

---

## Execution Trigger

Run **AFTER every non-trivial code modification task**

---

## Step 1 — Capture Intent

Summarize the original task in a structured way:

```json
{
  "intent": "...",
  "expected_files": [],
  "expected_functions": [],
  "constraints": []
}
```

---

## Step 2 — Detect Changes

### SAFE MODE (default)

```bash
git diff --name-only
git diff
```

### STRICT MODE (optional)

```bash
git add -A
git commit -m "temp: verification snapshot"
git diff HEAD~1 HEAD
```

> If strict mode is used, the commit MUST be flagged as temporary.

---

## Step 3 — Build Change Map

Generate a structured report:

```json
{
  "modified_files": [],
  "added_files": [],
  "deleted_files": [],
  "modified_functions": []
}
```

---

## Step 4 — Intent vs Reality Check

Validate:

* ❌ Files modified outside expected scope
* ❌ Missing expected modifications
* ❌ Over-modification (unrelated refactors)
* ❌ Silent behavioral changes

---

## Step 5 — Encoding Integrity Check

Verify:

* File encoding remains consistent (UTF-8 expected)
* No introduction of:

  * � (replacement characters)
  * mixed encodings
  * BOM inconsistencies
* Line endings consistency (LF vs CRLF)

Example checks:

```bash
file <filename>
```

Heuristics:

* Detect non-UTF8 sequences
* Detect invisible/control characters

---

## Step 6 — Regression Heuristics

Check for:

* Duplicate UI elements (e.g., multiple "Save" buttons)
* Broken handlers / missing bindings
* Dead code introduced
* Inconsistent naming (violating conventions)

---

## Step 7 — Risk Classification

```json
{
  "status": "OK | WARNING | CRITICAL",
  "issues": [],
  "confidence": 0-100
}
```

---

## Step 8 — Mandatory Report

Output:

```md
## Post Task Verification Report

### Intent
...

### Changes Detected
...

### Mismatches
...

### Encoding Check
...

### Risks
...

### Verdict
...
```

---

## Step 9 — Strict Mode Cleanup (if used)

If a temporary commit was created:

```bash
git reset HEAD~1
```

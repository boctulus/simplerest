---
name: task-decomposition-protocol
description: Before executing any complex task, evaluate if each step fits in a standard context window. If not, subdivide into intermediate steps. Every approved plan MUST be persisted in docs/to-do/ using `php com todo create`.
---

# SKILL: Task Decomposition Protocol

## Purpose

Prevent incomplete executions caused by steps that exceed a standard context window. Force plan persistence so that work is never lost and can be resumed by any agent.

---

## When to Invoke

Invoke this skill BEFORE starting any task that:

* Touches 3 or more files
* Spans multiple system layers (DB + backend + frontend)
* Requires output that would need 1500+ tokens to express
* Has unclear scope or multiple moving parts

Do NOT invoke for:
* Single-file fixes
* Copy/text changes
* Config changes with a clear 1-step outcome

---

## Step 1 — Complexity Assessment

Rate the task:

```
SIMPLE   → 1 step, 1 file, clear output
MODERATE → 2-4 steps, few files, one system layer
COMPLEX  → 5+ steps OR spans multiple layers/systems
```

If COMPLEX → continue to Step 2. Otherwise proceed directly.

---

## Step 2 — Draft the Plan

Break the task into steps. Each step MUST:

* Be completable in a single agent turn
* Have a single clear output (file created, function modified, etc.)
* Not depend on side effects from the same turn
* Be expressible in under 300 words

**Step size heuristics:**

| Too large (split it)                          | Right size                              |
|-----------------------------------------------|-----------------------------------------|
| "Implement the payments module"               | "Create `src/lib/db/payments.js`"       |
| "Set up auth and add RLS policies"            | "Add RLS policy for `orders` table"     |
| "Refactor the dashboard and fix routing"      | "Move sidebar config to `sidebar.js`"   |

---

## Step 3 — Validate Step Size

For each step, verify:

- [ ] Can it complete without needing output from another step in the same turn?
- [ ] Does it touch fewer than 4 files?
- [ ] Is its output verifiable (test, screenshot, or git diff)?
- [ ] Can it be described in 1 sentence?

If any answer is NO → split the step further.

---

## Step 4 — Present Plan for Approval

Show the plan to the user before executing. Format:

```
## Plan: [Task Name]

Complexity: COMPLEX
Steps: N

1. [Step title] — [1-sentence description]
2. [Step title] — [1-sentence description]
...

Persist to docs/to-do/? [yes/no — default yes for COMPLEX]
```

Wait for user approval. Do NOT start executing until approved.

---

## Step 5 — Persist Approved Plan (MANDATORY for COMPLEX tasks)

After user approves, persist IMMEDIATELY using:

```bash
php com todo create <kebab-case-task-name> \
  --title="<Task Title>" \
  --complexity=high \
  --current-step=1 \
  --tags="<relevant,tags>" \
  --for-agents=true
```

Then add each step as content in the created file.

**NEVER create the file manually.** Always use `php com todo create`.

If `docs/to-do/` does not exist yet → it will be created by the command.

---

## Step 6 — Execute Steps in Order

* Complete step N fully before starting step N+1
* Mark each step done as it completes using:

```bash
php com todo set-metadata --file=<file> --current-step=<N+1>
```

* If a step fails → stop, report, do NOT continue to next step

---

## Rules

1. **No plan, no execution** — For COMPLEX tasks, no step runs without an approved, persisted plan.
2. **One step per turn** — Never batch multiple steps in a single agent turn unless they are trivially independent (e.g., two unrelated file creations).
3. **Plans are persistent** — A plan in `docs/to-do/` survives context resets. Always resume from the persisted plan, not from memory.
4. **Steps must be atomic** — A step either completes fully or not at all. No half-done states.

---

## Anti-Patterns

* Starting implementation before writing the plan
* Writing a plan in chat without persisting it
* Steps like "implement feature X" with no concrete output defined
* Persisting the plan AFTER starting to execute

---

## Integration

* Works alongside `post-task-verification-strict` — verify each step after completion
* Works alongside `anti-hallucination-project-guard` — validate files exist before referencing them in steps
* Uses `php com todo create` (CLAUDE.md standard)

---

## Summary

| Phase       | Action                                                  |
|-------------|---------------------------------------------------------|
| Assessment  | Rate complexity: SIMPLE / MODERATE / COMPLEX            |
| Decompose   | Break into atomic steps, each completable in one turn   |
| Validate    | Each step passes size check before presenting           |
| Present     | Show plan to user, wait for approval                    |
| Persist     | `php com todo create` — MANDATORY for COMPLEX tasks    |
| Execute     | One step at a time, mark progress after each            |

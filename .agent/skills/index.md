# Skill Index — Master Routing Guide

Master index of all skills in `.agent/skills/`. Use this to decide **which** skill to invoke and **when**.

---

## Quick Decision Tree

```
User request → What kind of task is this?

├─ Refactoring or modifying existing production code?
│   └─ → code-quality-protocol (WORKFLOW)
│
├─ Deploying code to any environment?
│   └─ → release-deploy-protocol (WORKFLOW)
│
├─ Multiple skills could apply, need orchestration?
│   └─ → skill-orchestrator (WORKFLOW)
│
├─ Implementing code that needs external research?
│   └─ → fresh-research-protocol (COMPOSABLE)
│
├─ Documenting a bug/incident/error?
│   └─ → incident-docs-protocol (COMPOSABLE)
│
├─ UI / Frontend work?
│   ├─ Creating/refactoring a datagrid?    → adaptive-datagrid-contract
│   ├─ Creating a new reusable component? → svelte-custom-component-governance
│   ├─ Writing Svelte 5 code?             → svelte-implementer
│   ├─ Replicating from design file?      → ui-replication-from-design
│   ├- Reviewing UI consistency?          → ui-consistency-and-pattern-reuse
│   ├─ Auditing web design guidelines?    → web-design-guidelines
│   ├─ Enforcing external SVGs?           → external-svg-asset-enforcement
│   ├─ Managing sidebar items?            → sidebar-architecture-contract
│   ├─ Generating Svelte page inventory?  → svelte-inventory-generator
│   └─ Adding UI alerts?                  → alert-usage-contract
│
├─ Skills management?
│   ├─ Creating/editing a skill?          → skill-maker
│   ├─ Auditing/fixing skill files?       → skill-reviewer-protocol
│   ├─ Resolving skill conflicts?         → skill-conflict-resolution-protocol
│   └─ Updating this index?               → skill-index-maintainer
│
├─ Code quality / safety?
│   ├─ Preventing hallucinated references?→ anti-hallucination-project-guard
│   ├─ Defensive refactoring?             → code-defensive-refactoring
│   ├─ Enforcing naming conventions?      → code-naming-conventions-contract
│   ├─ Cross-layer naming consistency?    → cross-layer-naming-consistency-contract
│   ├─ Normalizing internal links?        → internal-link-normalizer
│   ├─ Post-task verification?            → post-task-verification
│   └─ Strict post-task verification?     → post-task-verification-strict
│
├─ Git & Deploy?
│   ├─ Atomic git operations?             → git-safety-protocol
│   ├─ Git snapshot for diff analysis?    → git-snapshot-verification
│   ├─ Production deployment (PM2)?       → production-deployment-protocol
│   └─ PWA compliance audit?              → pwa-governance
│
├─ Task & project management?
│   ├─ Decomposing a complex task?        → task-decomposition-protocol
│   ├─ Journaling active task?            → task-journaling
│   └─ Managing Kanban backlog?           → todo-kanban
│
├─ Prompt / output control?
│   ├─ Suspending prompt execution?       → prompt-execution-control-protocol
│   └─ Constraining output format?        → prompt-output-constraints-contract
│
├─ Data & testing?
│   ├─ Push notification architecture?    → push-notifications-architecture
│   └─ Test data governance?              → test-data-governance-and-safety
│
└─ None of the above?
    └─ ↓ Check detailed tables below ↓
```

---

## Skill Tables by Category

### 🔵 UI / Frontend Development

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `adaptive-datagrid-contract` | COMPOSABLE | Creating or refactoring a search-filter-datagrid | Using a custom `<table>` or external datagrid library |
| `alert-usage-contract` | STANDALONE | Adding UI alert/notification components | `adaptive-datagrid-contract` or `svelte-custom-component-governance` already covers it |
| `external-svg-asset-enforcement` | STANDALONE | Adding SVG graphics to Svelte 5 components | Icon is inline-only or dynamic; already covered by `ui-replication-from-design` |
| `sidebar-architecture-contract` | STANDALONE | Adding menu items, roles, debugging sidebar, or adding app context | Not a sidebar-related task |
| `svelte-custom-component-governance` | STANDALONE | Defining, validating, or building a custom Svelte 5 component | Using an existing component library; already covered by `ui-consistency-and-pattern-reuse` |
| `svelte-implementer` | STANDALONE | Writing Svelte 5 implementation code using `onevent` syntax | Using Svelte 4 `on:event` syntax; non-Svelte framework |
| `svelte-inventory-generator` | STANDALONE | Generating/refreshing the canonical page inventory for i18n | Not an i18n or Svelte page inventory task |
| `ui-consistency-and-pattern-reuse` | STANDALONE | Reviewing UI for visual consistency and pattern reuse | `ui-replication-from-design` is already active for a specific design |
| `ui-replication-from-design` | STANDALONE | Reproducing UI from Penpot/Figma/screenshot | Already covered by `ui-consistency-and-pattern-reuse` for structural review |
| `web-design-guidelines` | STANDALONE | Auditing UI code for web interface guidelines compliance | Already covered by `ui-consistency-and-pattern-reuse` or `ui-replication-from-design` |

### 🟢 Code Quality & Safety

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `anti-hallucination-project-guard` | STANDALONE | Verifying filesystem references, preventing hallucinated paths/modules | Task is purely conceptual with no file references |
| `code-defensive-refactoring` | COMPOSABLE | Refactoring existing production code, shared modules, or APIs | Writing new code from scratch; `code-quality-protocol` is already active |
| `code-naming-conventions-contract` | STANDALONE | Enforcing naming conventions and dependency protection rules | Already covered by `code-quality-protocol` or `code-defensive-refactoring` |
| `code-quality-protocol` | WORKFLOW | Existing production code is being refactored with structural changes | New code creation; deploy-only tasks |
| `cross-layer-naming-consistency-contract` | STANDALONE | Enforcing unified naming across frontend/backend layers | Single-layer task; already covered by `code-quality-protocol` |
| `internal-link-normalizer` | STANDALONE | Generating or fixing internal URLs, hash navigation | External URLs only; already covered by `code-naming-conventions-contract` |
| `post-task-verification` | STANDALONE | Verifying changes after task completion (standard) | `post-task-verification-strict` is required (fail-fast needed) |
| `post-task-verification-strict` | STANDALONE | Verifying changes after task completion (strict/fail-fast) | Standard verification is sufficient |

### 🟠 Git & Deploy

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `git-safety-protocol` | STANDALONE | Performing atomic git operations, decomposing commits | Read-only operations; already covered by `release-deploy-protocol` |
| `git-snapshot-verification` | STANDALONE | Creating isolated git snapshots for diff analysis | Already covered by `git-safety-protocol` or `post-task-verification` |
| `production-deployment-protocol` | COMPOSABLE | Deploying Node.js apps to production via PM2 | Staging/dev deploy; already covered by `release-deploy-protocol` |
| `pwa-governance` | STANDALONE | Auditing PWA compliance (manifest, SW, offline, icons) | Not a PWA project; already covered by `production-deployment-protocol` |
| `release-deploy-protocol` | WORKFLOW | Deploying code to any environment (staging/production) with git operations | Read-only or non-deploy tasks |

### 🟣 Research & Documentation

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `fresh-research-protocol` | COMPOSABLE | Implementing code requiring external libraries/APIs research | Simple, well-known implementation without research |
| `incident-docs-protocol` | COMPOSABLE | Documenting a production bug, incident, or error | Feature documentation; task documentation |
| `task-decomposition-protocol` | STANDALONE | Breaking a complex task into sub-tasks for context window limits | Simple one-step tasks |
| `task-journaling` | STANDALONE | Journaling the active task step-by-step in docs/pending/ | Task is complete; already covered by `todo-kanban` |
| `todo-kanban` | STANDALONE | Managing Kanban backlog in docs/to-do/ | Not a task management operation |

### ⚪ Skills & Agent Management

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `skill-conflict-resolution-protocol` | COMPOSABLE | Multiple skills could apply with conflicting rules | Only one skill applies; no ambiguity |
| `skill-index-maintainer` | COMPOSABLE | Creating/updating/auditing `.agent/skills/index.md` | Not an index-related task |
| `skill-maker` | STANDALONE | Creating or editing a SKILL definition file | Reading or invoking an existing skill |
| `skill-orchestrator` | WORKFLOW | Any task needing multi-skill orchestration with dependency enforcement | Single-skill tasks; already covered by a specific workflow |
| `skill-reviewer-protocol` | STANDALONE | Auditing, correcting, normalizing skill files | Creating new skills (use `skill-maker`); already covered by `skill-orchestrator` |

### 🟡 Prompt & Output Control

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `prompt-execution-control-protocol` | STANDALONE | Suspending or pausing current prompt execution | Continuing execution normally |
| `prompt-output-constraints-contract` | STANDALONE | Enforcing controlled reasoning modes with output constraints | No special output constraints needed |

### 🔴 Data & Testing

| Skill | Type | Invoke When | Do NOT Invoke When |
|-------|------|-------------|-------------------|
| `push-notifications-architecture` | STANDALONE | Greenfield push notification system design or full redesign | Adding a simple notification to existing system |
| `test-data-governance-and-safety` | STANDALONE | Flagging test data, managing test data schema/cleanup | Production data only |

---

## Workflow Dependency Trees

### `code-quality-protocol`

```
code-quality-protocol
├── anti-hallucination-project-guard
├── code-defensive-refactoring
│   ├── anti-hallucination-project-guard
│   └── code-naming-conventions-contract
├── code-naming-conventions-contract
├── cross-layer-naming-consistency-contract
└── skill-reviewer-protocol
```

### `release-deploy-protocol`

```
release-deploy-protocol
├── docker-network-reliability-guard ← external (not in .agent/skills/)
├── git-safety-protocol
└── production-deployment-protocol
    └── git-safety-protocol
```

### `skill-orchestrator`

```
skill-orchestrator
├── anti-hallucination-project-guard
└── context-sanitizer-contract ← external (not in .agent/skills/)
```

### Composable Dependency Trees

```
adaptive-datagrid-contract
├── component-creation-contract ← external (not in .agent/skills/)
└── view-standards-contract ← external (not in .agent/skills/)

code-defensive-refactoring
├── anti-hallucination-project-guard
└── code-naming-conventions-contract

fresh-research-protocol
└── anti-hallucination-project-guard

incident-docs-protocol
└── context-sanitizer-contract ← external (not in .agent/skills/)

production-deployment-protocol
└── git-safety-protocol

skill-conflict-resolution-protocol
└── anti-hallucination-project-guard

skill-index-maintainer
└── skill-reviewer-protocol
```

---

## Standalone Skills (Alphabetical)

```
alert-usage-contract
anti-hallucination-project-guard
code-naming-conventions-contract
cross-layer-naming-consistency-contract
external-svg-asset-enforcement
git-safety-protocol
git-snapshot-verification
internal-link-normalizer
post-task-verification
post-task-verification-strict
prompt-execution-control-protocol
prompt-output-constraints-contract
push-notifications-architecture
pwa-governance
sidebar-architecture-contract
skill-maker
skill-reviewer-protocol
svelte-custom-component-governance
svelte-implementer
svelte-inventory-generator
task-decomposition-protocol
task-journaling
test-data-governance-and-safety
todo-kanban
ui-consistency-and-pattern-reuse
ui-replication-from-design
web-design-guidelines
```

---

## Composable Skills (Alphabetical)

```
adaptive-datagrid-contract
code-defensive-refactoring
fresh-research-protocol
incident-docs-protocol
production-deployment-protocol
skill-conflict-resolution-protocol
skill-index-maintainer
```

---

## Anti-False-Positive Rules

1. **Workflow absorbs sub-skills** — If a workflow is active, its sub-skills are implicitly invoked. Do not call them separately.

2. **Single-responsibility trigger** — Only invoke a skill when its specific trigger condition is met. Do not invoke by proximity or similarity.

3. **Existing vs New** — Refactoring skills (`code-defensive-refactoring`, `code-quality-protocol`) are for existing code only. For new code, use the lifecycle or implementation skill directly.

4. **No double-invocation** — If a sub-skill appears in multiple active workflows, execute it only once.

5. **Context matters** — `push-notifications-architecture` is only for greenfield or full redesign, not for adding a simple notification. `skill-index-maintainer` is only for index.md operations, not for general skill management.

---

*Generated: 2026-06-13 | 37 skills indexed (3 WORKFLOW, 7 COMPOSABLE, 27 STANDALONE)*
*Warning: `antiTriggers` frontmatter key not found in any skill — anti-triggers derived from context.*
*External dependencies not in `.agent/skills/`: `component-creation-contract`, `context-sanitizer-contract`, `docker-network-reliability-guard`, `view-standards-contract`*

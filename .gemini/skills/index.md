# Skill Index — Master Routing Guide

Optimized for LLM invocation decisions. Maps 61 skills across categories with dependency trees and anti-false-positive rules.

**Note:** `antiTriggers` frontmatter is missing from all skills. Warnings are flagged per skill below but no automated inference was applied.

---

## Quick Decision Tree

```
User request → What kind of task is this?

├─ "Create or modify a view/page"
│   ├─ Admin panel page?             → create-admin-page
│   ├─ Template/rendering?           → view-engine
│   ├─ New Svelte 5 component?       → svelte-custom-component-governance
│   ├─ SVG asset rules?              → external-svg-asset-enforcement
│   ├─ Search/filter datagrid?       → adaptive-datagrid-contract
│   ├─ UI alerts?                    → alert-usage-contract
│   ├─ Internal link normalization?  → internal-link-normalizer
│   ├─ UI consistency/patterns?      → ui-consistency-and-pattern-reuse
│   ├─ UI from design files?         → ui-replication-from-design
│   └─ Web design audit?             → web-design-guidelines
│
├─ "Work with API/endpoints"
│   ├─ New API endpoint?             → create-api-endpoint-guide
│   ├─ Zero-config CRUD?             → automatic-endpoints
│   ├─ Nested CRUD?                  → subresources
│   ├─ Filter by related table?      → related-table-filter
│   ├─ Define routes?                → routing
│   ├─ Handle request/response?      → request-response
│   ├─ Consume external API?         → api-client
│   ├─ Auth endpoints (client)?      → auth-consumption
│   └─ Intercept controllers?        → middleware
│
├─ "Work with database"
│   ├─ Create/alter tables?          → migration-lifecycle
│   ├─ Define schemas?               → schemas
│   ├─ Build queries?                → query-builder
│   ├─ Validate data?                → validation-rules
│   ├─ Multi-tenant?                 → multi-tenant-config
│   ├─ EventBus / hooks?             → eventbus-hooks
│   └─ Cache queries/files?          → caching
│
├─ "Infrastructure / Security / Deploy"
│   ├─ Configure ACL?                → acl-config
│   ├─ Day-to-day ACL ops?           → acl-operations
│   ├─ Harden security?              → security-hardening
│   ├─ Deploy to production?         → deployment
│   ├─ Node.js PM2 deploy?           → production-deployment-protocol
│   ├─ Full release lifecycle?       → release-deploy-protocol
│   ├─ Git safety?                   → git-safety-protocol
│   └─ Git snapshot/diff?            → git-snapshot-verification
│
├─ "Refactoring / Code Quality / Design"
│   ├─ Refactor existing code?       → code-defensive-refactoring
│   ├─ Full code quality lifecycle?  → code-quality-protocol
│   ├─ Naming conventions?           → code-naming-conventions-contract
│   ├─ Cross-layer naming?           → cross-layer-naming-consistency-contract
│   ├─ CLI command design?           → command-design-conventions
│   ├─ Prevent interface bloat?      → prevent-interface-inflation
│   ├─ Research before impl?         → fresh-research-protocol
│   ├─ Post-task verification?       → post-task-verification
│   ├─ Strict verification?          → post-task-verification-strict
│   ├─ Push notifications arch?      → push-notifications-architecture
│   └─ Test data governance?         → test-data-governance-and-safety
│
├─ "Package / Module / Scripts"
│   ├─ Create a package?             → package-creator
│   ├─ Standalone CLI script?        → standalone-script
│   ├─ Use global helpers?           → helpers
│   └─ Translation / i18n?           → i18n
│
├─ "Docs / Tasks / Journals"
│   ├─ Incident documentation?       → incident-docs-protocol
│   ├─ Break down complex task?      → task-decomposition-protocol
│   ├─ Track in pending/?            → task-journaling
│   └─ Manage Kanban backlog?        → todo-kanban
│
├─ "Agent / Skill system"
│   ├─ Orchestrate multiple skills   → skill-orchestrator
│   ├─ Create/manage skill           → skill-maker
│   ├─ Review/fix skill              → skill-reviewer-protocol
│   ├─ Maintain skill index          → skill-index-maintainer
│   ├─ Resolve skill conflicts       → skill-conflict-resolution-protocol
│   ├─ Align with project            → anti-hallucination-project-guard
│   └─ Suspend/control prompts       → prompt-execution-control-protocol
│       → Constrain output           → prompt-output-constraints-contract
│
└─ None of the above?
    └─ ↓ Check specialized skills below ↓
```

---

## 🔵 UI — Views & Presentation

| Skill | Invoke When | Do NOT Invoke When |
|-------|-------------|-------------------|
| `adaptive-datagrid-contract` | Implementing a search/filter/datagrid with mobile-first, column limits, and action consistency | Simple list without search/filter/datagrid patterns |
| `alert-usage-contract` | Using UI alerts for user feedback in views | Already covered by `ui-consistency-and-pattern-reuse` |
| `create-admin-page` | Creating a new admin panel page (class + view + route) using Bootstrap 5 + AdminLTE | Modifying an existing page without creating a new one |
| `external-svg-asset-enforcement` | Enforcing external SVG assets over inline SVG markup in Svelte 5 components | No SVG assets in the project |
| `internal-link-normalizer` | Fixing or generating internal URLs to prevent relative path errors or invalid hash navigation | Using external URLs |
| `svelte-custom-component-governance` | Defining, validating, or building custom Svelte 5 components under strict simplicity rules | Modifying existing components without adding new ones |
| `ui-consistency-and-pattern-reuse` | Enforcing visual consistency, structural coherence, and reusable interface patterns | Single one-off component (use `svelte-custom-component-governance`) |
| `ui-replication-from-design` | Reproducing UI from Penpot/Figma/screenshot files adapting to the project's tech stack | No design file to replicate from |
| `view-engine` | Working with layouts, partials, assets, view caching, or the `view()` helper | Already using `create-admin-page` which handles views implicitly |
| `web-design-guidelines` | Reviewing UI code for Web Interface Guidelines compliance | Already covered by `ui-consistency-and-pattern-reuse` |

## 🟢 API — Endpoints & HTTP

| Skill | Invoke When | Do NOT Invoke When |
|-------|-------------|-------------------|
| `api-client` | Consuming an external API using SimpleRest's built-in HTTP client (cURL abstraction) | Need to create your own API endpoint (use `create-api-endpoint-guide`) |
| `auth-consumption` | Implementing login, register, refresh, logout, password reset, or email verification on the client side | Configuring server-side auth (use `security-hardening` or `acl-config`) |
| `automatic-endpoints` | Setting up zero-config REST CRUD from schemas with filtering, pagination, sorting, and ACL | Need custom business logic beyond CRUD (use `create-api-endpoint-guide`) |
| `create-api-endpoint-guide` | Creating a new API endpoint with migrations, schemas, models, controllers, and ACL | Already covered by `automatic-endpoints` for simple CRUD |
| `middleware` | Creating, registering, or using middleware to intercept/modify controller responses | Need to handle request/response directly (use `request-response`) |
| `related-table-filter` | Enabling filtering by related table fields through `$connect_to` and dot-notation | No related table filtering needed |
| `request-response` | Using PSR-7-style Request (Singleton) and Response (Singleton + immutable) classes in controllers | Already using middleware for response transformation |
| `routing` | Defining HTTP or CLI routes via WebRouter, CliRouter, FrontController, or package/module routing | Routes are auto-handled by `automatic-endpoints` |
| `subresources` | Implementing nested CRUD endpoints for related resources automatically inferred from schema relationships | Flat CRUD without nesting (use `automatic-endpoints` or `create-api-endpoint-guide`) |

## 🟠 DB — Database & Storage

| Skill | Invoke When | Do NOT Invoke When |
|-------|-------------|-------------------|
| `caching` | Setting up FileCache, DBCache, InMemoryCache, or query caching; managing cache via CLI | Need to store large relational data (use schemas + query-builder) |
| `eventbus-hooks` | Implementing Observer pattern / EventBus or model lifecycle hooks (beforeSave, afterSave, etc.) | Already covered by `schemas` which auto-generates hooks |
| `migration-lifecycle` | Creating, running, rolling back, or managing migrations in modules/packages | Schema-only changes without structural migration (use `schemas`) |
| `multi-tenant-config` | Configuring multiple database connections, table prefixes, schema directories, or multi-tenant setups | Single-tenant applications with one connection |
| `query-builder` | Building queries using SimpleRest Query Builder (all methods, Laravel differences, pitfalls) | Need raw SQL / PDO directly (but prefer QB when possible) |
| `schemas` | Creating, editing schemas as foundation for auto-endpoints, AutoJoins, validation, and relationships | Need to write raw SQL migrations (use `migration-lifecycle`) |
| `validation-rules` | Using the Validator class, schema-based validation, or i18n error messages | Already covered by `schemas` which auto-generates validation rules |

## 🔴 Infra — DevOps & Security

| Skill | Invoke When | Do NOT Invoke When |
|-------|-------------|-------------------|
| `acl-config` | Configuring ACL roles, inheritance, and resource permissions in `config/acl.php` | Day-to-day ACL operations like role assignment (use `acl-operations`) |
| `acl-operations` | Running CLI commands for ACL: role assignment, permissions, deny rules, debugging | Configuring the ACL structure/roles (use `acl-config`) |
| `deployment` | Deploying to production with Nginx, Apache, Docker, or performance optimization | Local development setup without deployment concerns |
| `git-safety-protocol` | Executing atomic workflows with integrated git safety constraints | Simple git operations (commit, push, pull) without safety needs |
| `git-snapshot-verification` | Creating isolated reversible git snapshots for accurate diff analysis without polluting history | Need permanent commits (use `git-safety-protocol`) |
| `production-deployment-protocol` | Safely deploying Node.js apps to production using PM2 with strict safety checks | PHP/Svelte deployment (use `deployment`) |
| `release-deploy-protocol` | Orchestrating the full release lifecycle: git safety → production deployment → docker network verification | Single deployment step without orchestration |
| `security-hardening` | Configuring JWT, ACL hardening, input sanitization, CSRF protection, and encryption | Day-to-day ACL management (use `acl-operations`) |

## 🟣 Code — Refactoring, Quality & Design

| Skill | Invoke When | Do NOT Invoke When |
|-------|-------------|-------------------|
| `code-defensive-refactoring` | Refactoring existing code with defensive discipline to preserve behavior | Writing new code (use the appropriate lifecycle skill) |
| `code-naming-conventions-contract` | Enforcing naming conventions and dependency protection across the codebase | Already covered by `code-quality-protocol` which invokes it |
| `code-quality-protocol` | Orchestrating the code quality lifecycle: anti-hallucination guard → defensive refactoring → naming conventions → cross-layer naming → skill review | Single refactoring step without full lifecycle |
| `command-design-conventions` | Designing CLI commands with semantic rules and safety constraints for read/query/destructive ops | Adding a single simple command without design complexity |
| `cross-layer-naming-consistency-contract` | Enforcing unified naming across all layers and preventing semantic drift | Already covered by `code-quality-protocol` which invokes it |
| `fresh-research-protocol` | Performing mandatory fresh research before implementing new features or making changes | Already researched the topic recently |
| `post-task-verification` | Enforcing post-task validation including change tracking, intent alignment, encoding integrity, and regression detection | Already covered by `post-task-verification-strict` for higher rigor |
| `post-task-verification-strict` | Enforcing strict post-task validation with fail-fast rules, intent alignment, encoding integrity, and scope control | Lower rigor validation is sufficient (use `post-task-verification`) |
| `prevent-interface-inflation` | Preventing premature abstractions and unnecessary interfaces during design | Already decided on interface usage (not relevant) |
| `prompt-execution-control-protocol` | Suspending or controlling execution of a user prompt | Normal execution flow without suspension needs |
| `prompt-output-constraints-contract` | Applying Controlled Reasoning Modes (CRM) with output constraints | No need to constrain LLM output |
| `push-notifications-architecture` | Selecting strategy for push notifications systems (greenfield or full redesign only) | Incremental changes to existing push notifications |
| `test-data-governance-and-safety` | Flagging test data with `is_test_data`, schema migrations, UI visibility controls, and cleanup | Production data only |

## ⚪ Agent & Process — Skills, Docs, CLI

| Skill | Invoke When | Do NOT Invoke When |
|-------|-------------|-------------------|
| `anti-hallucination-project-guard` | Enforcing alignment with real project structure; preventing hallucinated modules/files/commands | Already covered by `skill-orchestrator` which invokes it automatically |
| `helpers` | Using any of the 21 global helper files (debug, auth, DB, i18n, routing, config, etc.) | Need to create new helper files (use `package-creator` for custom helpers) |
| `i18n` | Implementing internationalization with gettext translations, .po/.mo files, and locale structure | Single-language applications without translation needs |
| `incident-docs-protocol` | Documenting a production bug, incident, or error — separates incident-specific context from reusable knowledge | Routine feature documentation |
| `package-creator` | Creating, configuring, and distributing packages with ServiceProviders, Composer, routes, and migrations | Simple module within existing package |
| `skill-conflict-resolution-protocol` | Detecting, preventing, and resolving conflicts between skills that cause ambiguity or inconsistency | Single skill active — no conflict possible |
| `skill-index-maintainer` | Creating, updating, regenerating, or auditing the master `index.md` routing guide | Already covered by `skill-orchestrator` or `skill-reviewer-protocol` |
| `skill-maker` | Creating and managing SKILL definitions for the agent | Editing existing skills (use `skill-reviewer-protocol` for fixes) |
| `skill-orchestrator` | Orchestrating execution of multiple skills with proper order, dependency management, and precondition enforcement | Running a single standalone skill (invoke directly) |
| `skill-reviewer-protocol` | Auditing, correcting, and normalizing defective or incomplete skills | Creating a new skill from scratch (use `skill-maker`) |
| `standalone-script` | Creating standalone CLI scripts that bootstrap SimpleRest core for DB, QB, helpers, and libs | Need a web endpoint (use `create-api-endpoint-guide`) |
| `task-decomposition-protocol` | Breaking down complex tasks that exceed context window limits into intermediate steps | Simple straightforward task fitting in one step |
| `task-journaling` | Creating ephemeral journals in `docs/pending/` before executing; updating step by step; deleting on completion | Long-term documentation (use `todo-kanban` for backlog) |
| `todo-kanban` | Managing the Kanban backlog in `docs/to-do/` with folder-based state transitions | Simple one-off note not tracked in backlog |

---

## Workflow Dependency Trees

### skill-orchestrator (WORKFLOW)
```
skill-orchestrator
├── anti-hallucination-project-guard
└── context-sanitizer-contract   ← not in .agent/skills/ (unresolved)
```

### code-quality-protocol (WORKFLOW)
```
code-quality-protocol
├── anti-hallucination-project-guard
├── code-defensive-refactoring
├── code-naming-conventions-contract
├── cross-layer-naming-consistency-contract
└── skill-reviewer-protocol
```

### release-deploy-protocol (WORKFLOW)
```
release-deploy-protocol
├── docker-network-reliability-guard   ← not in .agent/skills/ (unresolved)
├── git-safety-protocol
└── production-deployment-protocol
    └── git-safety-protocol   ← shared (ejecuta una sola vez)
```

### skill-index-maintainer (COMPOSABLE)
```
skill-index-maintainer
└── skill-reviewer-protocol
    ├── anti-hallucination-project-guard
    └── code-naming-conventions-contract   ← not in .agent/skills/ (unresolved)
```

### skill-conflict-resolution-protocol (COMPOSABLE)
```
skill-conflict-resolution-protocol
└── anti-hallucination-project-guard
```

### skill-reviewer-protocol (COMPOSABLE)
```
skill-reviewer-protocol
├── anti-hallucination-project-guard
└── code-naming-conventions-contract   ← not in .agent/skills/ (unresolved)
```

### adaptive-datagrid-contract (COMPOSABLE)
```
adaptive-datagrid-contract
├── component-creation-contract   ← not in .agent/skills/ (unresolved)
└── view-standards-contract   ← not in .agent/skills/ (unresolved)
```

### fresh-research-protocol (COMPOSABLE)
```
fresh-research-protocol
└── anti-hallucination-project-guard
```

### incident-docs-protocol (COMPOSABLE)
```
incident-docs-protocol
└── context-sanitizer-contract   ← not in .agent/skills/ (unresolved)
```

### production-deployment-protocol (COMPOSABLE)
```
production-deployment-protocol
└── git-safety-protocol
```

### related-table-filter (COMPOSABLE)
```
related-table-filter
├── automatic-endpoints
├── query-builder
└── subresources
```

---

## Standalone Skills (No Dependencies)

```
acl-config
acl-operations
alert-usage-contract
anti-hallucination-project-guard
api-client
auth-consumption
automatic-endpoints
caching
code-defensive-refactoring
code-naming-conventions-contract
command-design-conventions
create-admin-page
create-api-endpoint-guide
cross-layer-naming-consistency-contract
deployment
eventbus-hooks
external-svg-asset-enforcement
git-safety-protocol
git-snapshot-verification
helpers
i18n
internal-link-normalizer
middleware
migration-lifecycle
multi-tenant-config
package-creator
post-task-verification
post-task-verification-strict
prevent-interface-inflation
prompt-execution-control-protocol
prompt-output-constraints-contract
push-notifications-architecture
query-builder
request-response
routing
schemas
security-hardening
skill-maker
standalone-script
subresources
svelte-custom-component-governance
task-decomposition-protocol
task-journaling
test-data-governance-and-safety
todo-kanban
ui-consistency-and-pattern-reuse
ui-replication-from-design
validation-rules
view-engine
web-design-guidelines
```

---

## Composable Skills (Optional Dependencies)

```
adaptive-datagrid-contract
fresh-research-protocol
incident-docs-protocol
production-deployment-protocol
related-table-filter
skill-conflict-resolution-protocol
skill-index-maintainer
skill-reviewer-protocol
```

---

## Anti-False-Positive Rules

1. **Workflow absorbs sub-skills** — When `skill-orchestrator`, `code-quality-protocol`, or `release-deploy-protocol` is active, their sub-skills are implicitly invoked. Do not call them separately.

2. **Single-responsibility trigger** — Only invoke a skill when its specific condition is met. `create-api-endpoint-guide` is for custom endpoints; `automatic-endpoints` is for zero-config CRUD; `deployment` is for general deployment; `production-deployment-protocol` is specifically for Node.js PM2.

3. **Existing vs New** — `code-defensive-refactoring` is for existing code only. For new code, use the corresponding lifecycle skill. `fresh-research-protocol` is for new features; do not invoke for routine edits.

4. **No double-invocation** — If a sub-skill appears in multiple active workflows (e.g., `anti-hallucination-project-guard` appears under `skill-orchestrator`, `code-quality-protocol`, `skill-conflict-resolution-protocol`, `fresh-research-protocol`, and `skill-reviewer-protocol`), execute only once.

5. **Context matters** — `multi-tenant-config` is only for multi-tenant DB setups. `push-notifications-architecture` is only for greenfield or full redesign. `test-data-governance-and-safety` is only when test data management is needed.

---

## Unresolved Dependencies

The following skills are referenced in `REQUIRES` sections but have no SKILL.md in `.agent/skills/`:

- `component-creation-contract` — required by `adaptive-datagrid-contract`
- `context-sanitizer-contract` — required by `skill-orchestrator`, `incident-docs-protocol`
- `docker-network-reliability-guard` — required by `release-deploy-protocol`
- `view-standards-contract` — required by `adaptive-datagrid-contract`
- `code-naming-conventions-contract` (SKILL.md exists but listed as unresolved by dependency-tree — verify manually)

These act as **dead references** until their SKILL.md files are created.

---

## External / Global Skills (Not in .agent/skills/)

These skills are available at the user/global level and supplement the project index:

- `caveman` — Ultra-compressed communication mode for token efficiency
- `customize-opencode` — Configuring opencode's own settings (`opencode.json`, `.opencode/`, MCP, plugins)
- `find-skills` — Discovering and installing agent skills by topic

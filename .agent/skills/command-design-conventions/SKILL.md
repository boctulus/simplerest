---
name: command-design-conventions
description: Defines semantic rules and safety constraints for designing CLI-style commands, ensuring consistency between read, query, and destructive operations.
---

These principles applies for Command Line Interface (CLI) of whatever system ("node com", "php com", etc).


## Command Design Principles

### 1. Semantic Separation of Commands

Commands must reflect intent explicitly. Verbs are not interchangeable.

#### LIST commands (plural resources)

* Prefix: `list-<resource>`
* Purpose: retrieve collections
* May include optional filters
* Must always be safe (read-only)

Examples:

* `list-users --role=admin`
* `list-orders --status=pending`
* `list-products --category=electronics`

Rules:

* Must never require an identifier
* Must return zero or more results
* Filters are optional and additive

---

#### SHOW commands (single resource)

* Prefix: `show-<resource>`
* Purpose: retrieve a single, well-defined entity
* Must require a strict identifier

Examples:

* `show-user --email=user@example.com`
* `show-order --id=12345`

Rules:

* Must require at least one identifying flag:

  * `--id=`
  * `--email=`
  * `--slug=`
* If identifier is missing → command must fail
* Must never return collections

---

### 2. Filtering Rules

* `list-*` commands:

  * Filters are optional
  * Multiple filters are allowed
  * Filters narrow results

* `show-*` commands:

  * Filters are not optional substitutions for identity
  * At least one unique identifier is mandatory
  * Non-unique filters must not be accepted as sole selectors

---

### 3. Destructive Operations Safety

Any command that modifies or deletes data must include explicit confirmation.

#### Affected verbs:

* `delete-*`
* `truncate-*`
* `purge-*`
* `drop-*`
* any irreversible mutation command

#### Required flags (at least one):

* `--force`
* `--yes`
* `--confirm`

Examples:

* `delete-user --id=123 --confirm`
* `truncate-logs --force`
* `drop-table --name=users --yes`

Rules:

* Without confirmation flag → command must refuse execution
* Confirmation flags must never be implicit
* Interactive confirmation is optional but not sufficient alone for automation contexts

---

### 4. Safety and Predictability Constraints

* Read operations must never modify state
* Write operations must always be explicit
* Ambiguous commands must fail fast
* No hidden defaults for destructive behavior

---

### 5. Naming Consistency Rules

* Commands: `kebab-case`
* Flags: `--lowercase-with-dashes`
* Resource names must be plural in `list-*` and singular in `show-*`

Examples:

* `list-users`
* `show-user`
* `delete-session`

---

### 6. Failure Behavior

A command must fail when:

* Required identifier is missing in `show-*`
* Confirmation flag is missing in destructive commands
* Command scope is ambiguous (e.g. could return multiple results in a `show-*` context)

Failure should be:

* explicit
* deterministic
* non-recoverable without correction of input

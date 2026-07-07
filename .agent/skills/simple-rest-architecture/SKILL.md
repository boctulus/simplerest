---
name: simple-rest-architecture
description: Defines architectural responsibility boundaries in SimpleRest. Ensures Controllers orchestrate HTTP only, Models own persistence and domain operations, and framework abstractions are preferred over direct database access.
---

# SimpleRest Layer Boundaries

## Purpose

Preserve a clean separation between HTTP, domain logic, persistence and database access.

The objective is to:

- prevent Fat Controllers
- avoid bypassing the framework
- centralize business rules
- keep persistence logic maintainable
- reduce duplicated logic

---

# Layer Hierarchy (MANDATORY)

```
HTTP Request
      │
      ▼
ApiController
      │
      ▼
Model
      │
      ▼
Query Builder / Framework Persistence API
      │
      ▼
Database
```

Each layer has a single responsibility.

Do not skip layers without a documented technical reason.

---

# Controller Responsibilities

Controllers are responsible for HTTP orchestration.

Allowed responsibilities include:

- routing
- request parsing
- authorization checks
- invoking Model methods
- selecting HTTP status codes
- formatting HTTP responses
- framework lifecycle hooks

Controllers should remain thin.

---

# Controller Must NOT

Do not place any of the following inside Controllers:

- SQL
- persistence logic
- Query Builder chains
- database access
- entity manipulation
- business rules
- integrity checks
- tenant visibility logic
- duplicated domain logic

The Controller should express **what** is happening, not **how** it is implemented.

Example:

```
Controller
↓
$userModel->activate($id)
↓
HTTP Response
```

Not:

```
Controller
↓
SQL
↓
Response
```

---

# Model Responsibilities

Models own everything related to the entity itself.

This includes:

- queries
- scopes
- persistence
- business operations
- validations
- integrity checks
- lifecycle hooks
- entity-specific rules

If a rule exists because of the business domain rather than HTTP, it belongs in the Model.

Examples:

- activate
- deactivate
- archive
- restore
- soft delete
- permanent delete
- ownership validation
- association validation
- visibility rules

---

# Business Rules

Business rules must exist in exactly one place.

Never duplicate the same rule:

- once using Query Builder
- once using raw SQL
- once in multiple Controllers

Every rule should have a single source of truth.

---

# Tenant Scopes

Tenant visibility is a business rule.

The implementation must be centralized.

Controllers may invoke tenant scoping as part of the request lifecycle, but should not contain the implementation themselves.

Avoid maintaining multiple implementations of the same scope.

---

# Database Access

Prefer framework abstractions.

Preferred order:

```
Model
↓
Framework Query Builder
↓
Database
```

Direct database access should be exceptional rather than the default.

---

# Raw SQL

Raw SQL is acceptable only when justified.

Typical examples include:

- framework limitation
- vendor-specific SQL
- complex reporting queries
- verified performance bottlenecks
- DDL statements

Before introducing raw SQL, verify that the framework cannot express the operation adequately.

---

# Code Review Checklist

When reviewing a Controller, ask:

- Does it contain SQL?
- Does it manipulate persistence directly?
- Does it implement business rules?
- Does it duplicate Model logic?
- Does it bypass the framework?

If any answer is yes, the design should be reconsidered.

---

# Guiding Principle

Controllers coordinate.

Models decide.

The persistence layer executes.

The database stores data.

Keep each responsibility in its proper layer.
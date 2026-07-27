# Project Stage

## Current classification

**Stage:** `structural_mvp`

**Last reviewed:** YYYY-MM-DD

**Reviewed by:** Project owner

## Available classifications

Only one primary classification may be active at a time.

* `prototype`
* `structural_mvp`
* `initial_commercial_product`
* `mature_product`

## Development track

**Track:** `durable_product`

Allowed values:

* `disposable_prototype`
* `durable_product`

A project classified as `prototype` normally belongs to the `disposable_prototype` track.

The following stages belong to the `durable_product` track:

* `structural_mvp`
* `initial_commercial_product`
* `mature_product`

Moving from `prototype` to `structural_mvp` must be treated as an explicit architectural transition. It may require a rewrite, partial rewrite, migration, or replacement of prototype components.

Moving between stages in the durable-product track should normally preserve the central architecture and domain model.

## Current product objective

Describe the concrete purpose of the current stage.

Example:

> Validate whether condominium administrators can configure and operate a real condominium using the essential property, ownership, communication, operation, and financial workflows.

## Critical product journeys

List the end-to-end journeys whose weakest step can invalidate the current product stage.

1. Create or configure an organization.
2. Create a condominium.
3. Register properties.
4. Register owners and co-owners independently from login accounts.
5. Associate owners and co-owners with properties.
6. Grant or remove system access without altering ownership records.
7. Perform the core operational workflows selected for validation.

## Architectural invariants

These constraints must not be degraded by ordinary feature work or short-term shortcuts.

* Tenant data must remain correctly isolated.
* Domain entities must not be replaced by authentication accounts.
* Access membership and domain relationships must remain independent.
* Persistent data must preserve referential and business integrity.
* Authorization must be enforced server-side.
* Existing architectural boundaries must not be bypassed without an explicit decision.
* Temporary implementation shortcuts must not silently become permanent domain rules.

Add project-specific invariants below:

* ...
* ...

## Acceptable incompleteness

These elements may remain incomplete during the current stage without invalidating the product architecture.

* Advanced reporting.
* Non-essential integrations.
* Highly polished user experience.
* Automation of operations that can temporarily be performed manually.
* Optimization for unproven scale.
* Rare edge cases that do not compromise security, money, tenant isolation, or data integrity.

Add project-specific accepted limitations below:

* ...
* ...

## Unacceptable shortcuts

The following shortcuts are not acceptable at the current stage:

* Introducing false domain concepts to avoid a proper relationship or entity.
* Corrupting or weakening the existing domain model.
* Coupling domain existence to login-account existence without a real business requirement.
* Bypassing authorization because a feature is still being validated.
* Storing production-relevant data in structures known to require replacement.
* Introducing cross-tenant access risks.
* Creating irreversible migrations without rollback or recovery consideration.
* Expanding a local workaround across multiple architectural layers.

## Current quality expectations

### Domain modelling

Required level:

> The central domain must be modelled correctly. Optional features may be omitted, but included core concepts must not be represented through knowingly false abstractions.

### Data integrity

Required level:

> Persistent business data must be migratable, internally consistent, and protected from known corruption paths.

### Security and authorization

Required level:

> Authentication, authorization, tenant isolation, and sensitive operations must be implemented as production-relevant concerns.

### Testing

Required level:

> Critical invariants and core product journeys must have targeted automated or reproducible verification. Exhaustive coverage is not required.

### Operations

Required level:

> Deployment and recovery must be sufficiently reliable for the current user exposure. Advanced observability may remain incomplete.

### Scalability

Required level:

> Avoid proven structural bottlenecks, but do not optimize for hypothetical scale without evidence.

### User experience

Required level:

> Core workflows must be understandable and usable. Secondary polish may be deferred.

## Complexity policy

Complexity is justified when it protects one or more of the following:

* a central domain invariant;
* tenant isolation;
* authorization;
* business data integrity;
* financial correctness;
* a critical product journey;
* a migration path that would otherwise become disproportionately expensive;
* an architectural boundary already adopted by the durable product.

Complexity is not justified merely because:

* it is theoretically cleaner;
* it handles hypothetical future requirements;
* it creates a more generic framework;
* it eliminates harmless duplication;
* it supports scale that has not been demonstrated;
* it completes adjacent work that does not block the current objective.

## Decision policy

Every significant architectural or structural decision must explicitly state:

1. The current project stage.
2. The active development track.
3. The critical journey or invariant affected.
4. Why the selected complexity is appropriate for this stage.
5. Which simpler alternative was considered.
6. Why that simpler alternative was rejected or accepted.
7. What is intentionally deferred.
8. Whether the decision remains valid after the next expected stage transition.

## Stage transition conditions

### Prototype to structural MVP

This is not assumed to be an incremental promotion.

Before transition:

* identify disposable components;
* decide what may be preserved;
* validate the central domain model;
* define the durable architecture;
* plan rewrite or migration work;
* remove assumptions that were acceptable only for demonstration;
* establish production-relevant security and data integrity.

### Structural MVP to initial commercial product

The central architecture should normally remain valid.

The transition primarily adds:

* operational reliability;
* onboarding readiness;
* supportability;
* billing or commercial workflows;
* stronger regression protection;
* auditability where necessary;
* production data migration discipline;
* complete treatment of critical failure cases.

### Initial commercial product to mature product

The transition primarily adds:

* scale;
* observability;
* performance;
* stronger automation;
* broader integrations;
* advanced administration;
* formal operational processes;
* more comprehensive test coverage;
* resilience and recovery capabilities.

## Review triggers

This document must be reviewed when:

* the product obtains its first real users or customers;
* the project changes its target market or principal use case;
* a prototype is proposed for real production use;
* an architectural rewrite is proposed;
* a temporary workaround begins affecting additional modules;
* production data is introduced;
* payments or financially sensitive operations are introduced;
* tenant, identity, membership, or authorization models change;
* the current classification no longer explains actual engineering decisions.

## Decision history

### YYYY-MM-DD — Initial classification

**Previous stage:** None
**New stage:** `structural_mvp`

**Reason:**

Describe why this stage accurately represents the project.

**Consequences:**

Describe what implementation standards now apply and which prototype shortcuts are no longer acceptable.

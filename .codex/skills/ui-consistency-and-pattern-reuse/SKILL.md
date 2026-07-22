---
name: ui-consistency-and-pattern-reuse
description: >
  Enforces visual consistency, structural coherence, and reusable interface
  patterns across all views, components, cards, tables, forms, and dashboards.
  Prioritizes standardization, predictable layouts, and reuse of existing UI
  patterns while preserving controlled creativity and contextual flexibility.
---

# SKILL: UI Consistency and Pattern Reuse

## Objective

All interfaces must feel like part of the same product ecosystem.

A user navigating between views should immediately recognize:

* spacing behavior
* card hierarchy
* visual density
* typography rhythm
* button semantics
* metric presentation
* color usage
* component proportions
* interaction patterns

Consistency is not optional.

Visual incoherence is considered a UI defect.

---

# Core Principles

## 1. Standardize Before Inventing

Before creating new UI patterns:

* inspect existing views
* reuse existing structures
* reuse existing utility classes
* reuse spacing conventions
* reuse component compositions

If an existing solution already solves the problem adequately:

* copy the pattern
* adapt minimally
* preserve behavioral consistency

Do not create stylistic variants unnecessarily.

---

## 2. Similar Information Must Look Similar

Equivalent semantic content must share:

* dimensions
* spacing
* alignment
* typography
* component structure
* visual hierarchy

Examples:

* metric cards should share height
* action buttons should share sizing rules
* tables should share density and spacing
* filters should share layout structure
* dashboard sections should share rhythm

Avoid:

* random heights
* isolated colors
* arbitrary paddings
* inconsistent border radius
* inconsistent icon sizing
* one-off layouts

---

## 3. Reuse Existing Visual Language

When a view already establishes a visual language:

* extend it
* do not fight it

Examples:

* if metric cards use `fp-metric-card`, all metric cards should use it
* if dashboards use soft gradients, maintain the same system
* if forms use floating labels, preserve the pattern
* if pages use icon-title-description headers, continue using them

New elements must feel native to the existing interface.

---

## 4. Consistency Inside the Same View Is Mandatory

Within the same screen:

* equal components must have equal sizing
* equal cards must align vertically
* grids must behave predictably
* spacing rhythm must remain stable

Never allow:

* one card taller than others without semantic reason
* isolated color systems
* different padding strategies
* different border treatments
* different typography scales

If components belong to the same group:
they must visually behave as a system.

---

# Pattern Reuse Rules

## Reuse Existing Components Aggressively

Prefer:

* extending
* parameterizing
* composing

Instead of:

* duplicating
* recreating
* restyling existing patterns

Before creating a new component:

1. Search existing views
2. Search shared partials/components
3. Search utility classes
4. Search layout conventions

Only create new patterns when:

* the UX problem is genuinely different
* existing patterns create friction
* semantic meaning changes substantially

---

## Copy Proven Layout Structures

If another view already solved:

* dashboard metrics
* filters
* empty states
* data tables
* detail headers
* action bars
* section grouping

Prefer copying the structure and adapting content.

Do not redesign solved problems repeatedly.

---

# Visual Hierarchy Rules

## Maintain Predictable Emphasis

Primary actions:

* must always dominate secondary actions

Critical information:

* must always be visually prioritized

Metric cards:

* should follow the same typography scale

Avoid:

* random font sizes
* arbitrary emphasis
* accidental focal points

---

## Color Usage Must Be Systematic

Colors are semantic.

Do not assign colors arbitrarily.

If the system already uses:

* purple for primary metrics
* teal for success
* orange for warnings
* cyan for informational states

Maintain those semantics consistently.

Never introduce isolated color decisions without system-level justification.

---

# Layout Consistency Rules

## Grid Behavior Must Be Predictable

Cards in the same row:

* should share height
* should align correctly
* should preserve responsive behavior

Use:

* `align-items-stretch`
* flex-based equal height patterns
* shared card wrappers

Avoid:

* manual height hacks
* arbitrary margins
* inconsistent responsive breakpoints

---

## Spacing Must Follow Rhythm

Spacing should feel intentional and repeatable.

Avoid random combinations like:

* `mb-2` next to `mb-5`
* mixed padding scales
* inconsistent section separation

Prefer consistent spacing scales across:

* cards
* forms
* sections
* tables
* modals

---

# Creativity Rules

## Creativity Is Allowed Inside System Boundaries

Consistency does not mean visual stagnation.

Creativity is encouraged when:

* it preserves product identity
* it improves clarity
* it improves usability
* it extends the design language naturally

Good creativity:

* feels inevitable
* feels integrated
* feels like an evolution

Bad creativity:

* feels isolated
* breaks established patterns
* introduces inconsistency
* prioritizes novelty over coherence

---

# Anti-Patterns

## Forbidden UI Behaviors

Do not:

* mix unrelated card styles in the same section
* create one-off button styles
* invent random spacing systems
* mix incompatible border radius styles
* mix visual densities arbitrarily
* use inconsistent typography scales
* create visually dominant secondary information
* introduce isolated color logic
* redesign existing solved UI patterns without reason

---

# Decision Framework

Before modifying UI, always ask:

1. Does a similar pattern already exist?
2. Can this reuse an existing component?
3. Will this feel visually native?
4. Will this create inconsistency elsewhere?
5. Will users perceive this as part of the same system?
6. Are sibling components visually coherent?
7. Does spacing/alignment remain predictable?
8. Is creativity helping clarity or just adding variation?

If any answer indicates inconsistency:
refactor before continuing.

---

# Enforcement Examples

## Good

* All dashboard metric cards share:

  * same height
  * same spacing
  * same structure
  * same typography rhythm
  * same interaction model

* New views reuse existing:

  * toolbar patterns
  * table layouts
  * section headers
  * action positioning

* Variants extend shared base classes.

---

## Bad

* One metric card uses custom gradients while others use Bootstrap defaults
* Three cards align differently
* One table uses compact density while another uses large spacing without reason
* Different pages solve identical problems with completely different layouts
* Random color usage without semantic consistency

---

# Final Rule

Users should perceive the product as:

* coherent
* intentional
* predictable
* unified
* professionally designed

Not as a collection of unrelated screens made independently.

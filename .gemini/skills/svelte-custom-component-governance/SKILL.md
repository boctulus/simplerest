---
name: svelte-custom-component-governance
description: Define, validate and build custom components (Svelte 5) under strict rules of simplicity, consistency and automatic behavior. Prevents over-engineering and complex APIs.
---

# Custom Component Governance (Svelte 5)

## 1. Purpose

This SKILL defines **when and how to create custom components**.

Its goal is NOT to encourage component creation, but to **restrict it** to cases where there is a clear structural advantage over using libraries or inline code.

---

## 2. Entry Rule (MANDATORY)

A component can ONLY be created if it meets ALL conditions:

### ✔ 2.1. Can be described in 1 sentence

Valid examples:
- DataGrid → "renders mobile/desktop adaptive lists"
- Pager → "automatically switches between load-more and pagination"

If not possible → ❌ DO NOT create component

---

### ✔ 2.2. Has real reusability

Must be used in multiple views.

If specific to a single screen → ❌ DO NOT create component

---

### ✔ 2.3. Has clear structural advantage

Must resolve at least one:

- Non-trivial responsive behavior (not just CSS)
- Strong API simplification vs libraries
- UX standardization

If no clear advantage → ❌ use library or inline

---

### ✔ 2.4. Ultra simple API

- Minimal props
- No redundant configurations
- No ambiguous combinations

If requires many props → ❌ bad design

---

## 3. Design Rules (MANDATORY)

### 3.1. REAL Mobile-first

The component must:

- Work on mobile first
- Automatically adapt to desktop

❌ FORBIDDEN:
```svelte
<Component mode="mobile" />
```

✔ CORRECT:

- Component detects and adapts behavior automatically

---

### 3.2. Automatic behavior

The component decides internally:

- Responsive behavior
- Defaults
- Simplifications

❌ DO NOT expose unnecessary decisions to the user

---

### 3.3. Memorable API

Usage must be obvious without documentation.

Example:

```svelte
<Pager />
<DataGrid items={data} />
```

If requires reading docs → ❌ bad design

---

### 3.4. No hidden "magic"

Must be:

- Predictable
- Deterministic

❌ FORBIDDEN:

- Unexpected side effects
- Hard-to-understand implicit behavior

---

## 4. Complexity Constraints (CRITICAL)

### ❌ 4.1. Complex slots prohibited

DO NOT use:

- Render props
- Complex dynamic templates
- Multiple slots with logic

---

### ❌ 4.2. Excessive flexibility

DO NOT try to cover all cases.

If exceptions appear:

→ Resolve them OUTSIDE the component

---

### ✔ 4.3. Use "format" instead of custom render

Example:

```svelte
<DataGrid format={{ price: 'currency' }} />
```

Instead of:

```svelte
<DataGrid let:item>
  <span>{formatPrice(item.price)}</span>
</DataGrid>
```

---

## 4.4. Modal con tabs: campos requeridos solo en tab principal

### ❌ PROHIBIDO — Campos `required` en tabs secundarias

**Nunca colocar campos obligatorios (`required`) en tabs que no sean la primera.**

**Motivo:** El usuario puede intentar guardar sin haber visitado la tab secundaria. La validación fallará sin dar feedback claro de dónde está el error — UX confusa y rota.

**Regla:**
> Todo campo `required` DEBE estar en la **primera tab** del modal.

```
✔ Tab 1 (General) → nombre*, precio*     ← required
   Tab 2 (Extras)  → descripción         ← solo opcionales

❌ Tab 1 (General) → nombre*
   Tab 2 (Extras)  → precio*             ← required oculto = PROHIBIDO
```

Esta regla aplica a `ModalTabs`, `FormModalTabs` y cualquier componente modal con pestañas.

---

## 5. Anti-patterns (FORBIDDEN)

### ❌ Bloated API

```svelte
<Component
  size="sm"
  variant="primary"
  responsive
  mobileMode="stack"
  desktopMode="grid"
/>
```

---

### ❌ Conditional props

Props that only apply in certain cases.

---

### ❌ Redundant configuration

If the component can infer something → DO NOT expose it

---

### ❌ "Framework" component

Generic components that try to solve everything.

---

## 6. Evaluation before implementation

Mandatory checklist:

- [ ] Can it be explained in 1 sentence?
- [ ] Is it used in multiple views?
- [ ] Does it have clear advantage vs libraries?
- [ ] Is the API memorable?
- [ ] Does it work mobile-first without extra props?
- [ ] Does it avoid complex slots?
- [ ] Does it avoid unnecessary props?

If any is NO → ❌ DO NOT IMPLEMENT

---

## 7. Implementation Strategy (Svelte 5)

### 7.1. Philosophy

- Strong internal logic
- Minimal external API

---

### 7.2. Recommended structure

```
/components/
  data-grid/
    DataGrid.svelte
    DataGrid.types.ts
    DataGrid.logic.ts
```

---

### 7.3. Responsiveness

Must be resolved internally:

- Media queries
- Resize observer
- Centralized breakpoints

NOT via props

---

### 7.4. Internal state

Allowed if:

- Does not break predictability
- Does not introduce side effects

---

## 8. Correct example

```svelte
<DataGrid items={orders} />
<Pager total={100} />
```

✔ Simple
✔ Consistent
✔ No unnecessary decisions

---

## 9. Incorrect example

```svelte
<DataGrid
  items={orders}
  renderRow={...}
  mobileTemplate={...}
  desktopTemplate={...}
  variant="complex"
/>
```

❌ Over-designed
❌ Hard to use
❌ Breaks consistency

---

## 10. Final rule

> If the component needs explanation, it already failed.

---

## 11. Architectural consequence

This SKILL enforces:

- Fewer components
- Better quality
- Greater consistency
- Better LLM performance

And avoids:

- Useless abstractions
- Complex APIs
- Silent technical debt

---

## ⚡ Svelte 5 Reactivity Discipline (MANDATORY)

### Rule: isolate dependencies in reactive blocks

A `$:` block tracks ALL variables read inside as dependencies.
If a block mixes initialization (triggered by `open`) with reading `data`, it
re-executes when `data` changes — causing unexpected side effects.

#### ❌ Forbidden: reactive block with mixed dependencies

```svelte
// Tracks `open`, `data`, `tabs` — re-executes when user types in form
$: if (open) {
  formData = { ...data };   // necessary
  activeTabId = tabs[0].id; // ← BUG: resets when `data` changes
}
```

#### ✅ Correct: separate by purpose and dependency

```svelte
// Only depends on `open` — use beforeUpdate to detect false→true transition
let _prevOpen = false;
beforeUpdate(() => {
  if (open && !_prevOpen) {
    activeTabId = tabs[0].id; // only when modal opens
  }
  _prevOpen = open;
});

$: document.body.style.overflow = open ? 'hidden' : '';
```

### Rule: do not mix initialization with operational state

- State initialization (tab reset, form reset) must execute ONCE when modal opens
- User changes during operation MUST NOT trigger initializations
- Use `beforeUpdate` with previous state guard to detect boolean prop transitions

---

## 🔍 Critical observations

### 1. Risk: "too automatic"

If you hide too much:

- Debugging becomes difficult
- Edge cases become impossible

👉 Solution: allow **minimal and explicit override**, but rare

---

### 2. Risk: excessive rigidity

With such strict rules:

- You may end up duplicating logic outside the component

👉 Tradeoff: simplicity vs flexibility

---

### 3. Architectural alternative (very important)

Before creating components:

👉 Evaluate this:

**Could it be a "pattern" instead of a component?**

Examples:

- Helper functions
- Reusable stores
- Composables (Svelte stores)

Often this is **better than a component**

---

## 💣 Conclusion

This SKILL is **very good**, but only if applied with extreme discipline:

- Create few components
- Make them very simple
- Kill any attempt at over-design

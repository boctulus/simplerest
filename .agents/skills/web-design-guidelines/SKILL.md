---
name: web-design-guidelines
description: Review UI code for Web Interface Guidelines compliance (context-aware)
metadata:
  author: boctulus
  version: "1.0.0"
  argument-hint: <file-or-pattern>
---

# Web Interface Guidelines (Production-Ready)

Review these files: $ARGUMENTS  
Output: concise, high signal. Skip explanations unless fix is non-obvious.

---

# Severity

- [BLOCKER] – breaks accessibility, UX, or correctness
- [HIGH] – strong best practice, should fix
- [MEDIUM] – contextual, validate before flagging
- [LOW] – stylistic / polish

---

# Rules

## Accessibility

- [BLOCKER] Icon-only buttons need `aria-label`
- [BLOCKER] Form controls need `<label>` or `aria-label`
- [BLOCKER] Use semantic elements (`<button>`, `<a>`, `<input>`) instead of `<div onClick>`
- [BLOCKER] Images need `alt` (or `alt=""` if decorative)

- [HIGH] Decorative icons should have `aria-hidden="true"`
- [HIGH] Async updates (toasts, validation) should use `aria-live="polite"`

- [MEDIUM] Keyboard handlers (`onKeyDown`) ONLY if using non-semantic elements
- [MEDIUM] Use semantic HTML first, then enhance with ARIA (`aria-expanded`, etc.)

- [LOW] Headings should be hierarchical (`<h1>`–`<h6>`) (layout-level concern)
- [LOW] Skip links apply at app layout level, not per component

---

## Focus States

- [BLOCKER] No `outline: none` without visible focus replacement
- [BLOCKER] Interactive elements must have visible focus state

- [HIGH] Prefer `:focus-visible` over `:focus`
- [MEDIUM] Use `:focus-within` for grouped controls

---

## Forms

- [BLOCKER] Inputs must have accessible labels
- [BLOCKER] Do NOT block paste (`onPaste` + `preventDefault`)

- [HIGH] Use correct `type` (`email`, `tel`, etc.)
- [HIGH] Use meaningful `name` and proper `autocomplete` values (avoid disabling)

- [MEDIUM] Disable spellcheck for emails/codes if needed
- [MEDIUM] Labels should be clickable (`htmlFor` or wrapping)

- [MEDIUM] Controlled inputs must be performant (debounce or isolate state)
- [MEDIUM] Submit button: disable only during request, show loading state

- [HIGH] Errors should be inline and focus first error on submit

- [LOW] Placeholders may include examples (ellipsis optional, stylistic)

- [MEDIUM] Warn before navigation if form has unsaved changes

---

## Animation

- [BLOCKER] Must respect `prefers-reduced-motion`

- [HIGH] Animate only `transform` and `opacity` where possible
- [BLOCKER] Avoid `transition: all`

- [MEDIUM] Animations should be interruptible if user interaction is involved
- [LOW] Set correct `transform-origin`

---

## Typography

- [LOW] Prefer `…` over `...`
- [LOW] Use curly quotes when applicable
- [LOW] Use non-breaking spaces for units (`10 MB`)

- [MEDIUM] Use `font-variant-numeric: tabular-nums` for numeric tables
- [LOW] Use `text-wrap: balance` for headings

---

## Content Handling

- [BLOCKER] UI must handle empty states (no broken renders)
- [HIGH] Handle long content (`truncate`, `break-words`, etc.)

- [HIGH] Flex children should use `min-w-0` when truncation is expected

- [MEDIUM] Consider extreme user input lengths (very short / very long)

---

## Images

- [HIGH] Provide intrinsic sizing (`width/height` OR `aspect-ratio`)
- [HIGH] Lazy load non-critical images (`loading="lazy"`)

- [MEDIUM] Prioritize above-the-fold images if performance-critical

---

## Performance

- [BLOCKER] Avoid layout reads in render (`getBoundingClientRect`, etc.)

- [HIGH] Avoid large `.map()` without pagination or virtualization IF performance degrades

- [MEDIUM] Virtualize large/unbounded lists when needed (not fixed threshold)

- [MEDIUM] Batch DOM reads/writes when doing measurements

- [HIGH] Preconnect to critical external domains

- [HIGH] Preload critical fonts with `font-display: swap`

---

## Navigation & State

- [BLOCKER] Navigation must use `<a>` / `<Link>` (not `onClick`)

- [HIGH] Persist meaningful, shareable state in URL (filters, pagination)
- [MEDIUM] Do NOT persist ephemeral UI state (hover, temporary toggles)

- [HIGH] Destructive actions must have confirmation OR undo mechanism

---

## Touch & Interaction

- [MEDIUM] Use `touch-action: manipulation` selectively (avoid breaking gestures)

- [MEDIUM] Prevent scroll chaining in modals (`overscroll-behavior: contain`)

- [LOW] Set `-webkit-tap-highlight-color` intentionally

- [MEDIUM] Avoid `autoFocus` if it triggers unwanted mobile keyboard

---

## Safe Areas & Layout

- [MEDIUM] Handle safe areas (`env(safe-area-inset-*)`) for full-bleed layouts

- [HIGH] Prevent unwanted horizontal scroll (`overflow-x-hidden` where appropriate)

- [HIGH] Prefer flex/grid over JS layout calculations

---

## Dark Mode & Theming

- [HIGH] Use `color-scheme` appropriately

- [MEDIUM] Ensure `<meta name="theme-color">` matches UI

- [MEDIUM] Native inputs/selects must be styled explicitly in dark mode

---

## Locale & i18n

- [BLOCKER] Use `Intl.DateTimeFormat` for dates
- [BLOCKER] Use `Intl.NumberFormat` for numbers/currency

- [HIGH] Detect language via browser (`navigator.languages`), not IP

- [MEDIUM] Use `translate="no"` for code/brand tokens

---

## Hydration Safety

- [BLOCKER] Inputs with `value` must have `onChange` (or use `defaultValue`)

- [HIGH] Avoid server/client mismatches (dates, random values)

- [MEDIUM] Use `suppressHydrationWarning` only when justified

---

## Hover & Interactive States

- [HIGH] Interactive elements must have hover/active/focus states

- [MEDIUM] States should increase contrast vs rest state

---

## Content & Copy

- [LOW] Prefer active voice
- [LOW] Use clear, specific button labels

- [MEDIUM] Errors should include resolution guidance

- [LOW] Use numerals for counts ("8 items")

---

## Additional Critical Rules (Missing Before)

### Modals & Dialogs

- [BLOCKER] Must trap focus
- [BLOCKER] Must restore focus on close
- [BLOCKER] Use `role="dialog"` and `aria-modal="true"`

---

### Error Handling

- [HIGH] UI should not crash entirely (use error boundaries)

---

### Loading States

- [HIGH] Avoid spinner-only UX for slow loads; prefer skeletons where possible

---

### Color Contrast

- [BLOCKER] Text must meet WCAG contrast ratios

---

# Anti-patterns (Always Flag)

- [BLOCKER] `user-scalable=no` or `maximum-scale=1`
- [BLOCKER] `onPaste` + `preventDefault`
- [BLOCKER] `outline: none` without replacement
- [BLOCKER] `<div>`/`<span>` used as buttons
- [BLOCKER] Icon buttons without `aria-label`
- [BLOCKER] Inputs without labels
- [BLOCKER] Hardcoded date/number formats
- [BLOCKER] Navigation via `onClick` instead of links

- [HIGH] `transition: all`
- [HIGH] Large arrays rendered without considering performance

---

# Output Format

Group by file. Use `file:line`.

Example:

## src/Button.tsx

src/Button.tsx:42 [BLOCKER] icon button missing aria-label  
src/Button.tsx:18 [BLOCKER] input lacks label  
src/Button.tsx:55 [HIGH] missing prefers-reduced-motion handling  
src/Button.tsx:67 [HIGH] transition: all → specify properties  

## src/Modal.tsx

src/Modal.tsx:12 [BLOCKER] missing focus trap  
src/Modal.tsx:34 [MEDIUM] overscroll-behavior missing  

## src/Card.tsx

✓ pass

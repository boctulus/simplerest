---
name: internal-link-normalizer
description: Enforces correct internal URL generation, preventing relative path errors and invalid hash navigation.
---

# SKILL: Internal Link Normalizer

## Purpose

Ensure all internal links are **absolute from root** when they involve hash navigation, preventing navigation bugs when the current page is not `/`.

---

## Core Rule

> NEVER generate relative internal links.

All internal URLs MUST:

* Start with `/`
* Be valid from any route depth
* Work regardless of current location

---

## Hash Navigation Rule

### ❌ Invalid

```
#services
#contact
```

### ✅ Valid

```
/#services
/#contact
```

### Why

Relative hash links append to the current path:

* Current: `/pricing`
* Link: `#services`
* Result: `/pricing#services` ❌

Correct behavior:

* Link: `/#services`
* Result: `/#services` ✅

---

## Internal Path Rules

### ❌ Invalid

```
services
pricing
about
```

### ✅ Valid

```
/services
/pricing
/about
```

---

## Cross-page + hash

### ❌ Invalid

```
pricing#faq
```

### ✅ Valid

```
/pricing#faq
```

---

## Canonical Format

All internal links must follow:

```
/path
/path#section
/#section
```

---

## Exceptions

Allowed ONLY for:

* External URLs (`https://...`)
* Mail links (`mailto:`)
* Phone links (`tel:`)

---

## Implementation Guidance

When generating links:

1. Detect if link is internal
2. If it does NOT start with `/`, prepend `/`
3. If it starts with `#`, convert to `/#`
4. Preserve hash fragments

---

## Examples

| Intent              | Wrong         | Correct        |
| ------------------- | ------------- | -------------- |
| Section in homepage | `#services`   | `/#services`   |
| Pricing page        | `pricing`     | `/pricing`     |
| Pricing FAQ         | `pricing#faq` | `/pricing#faq` |
| Contact section     | `#contact`    | `/#contact`    |


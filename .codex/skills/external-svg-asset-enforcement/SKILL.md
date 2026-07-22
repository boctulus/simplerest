---
name: external-svg-asset-enforcement
description: Enforce external SVG assets over inline SVG markup in Svelte 5 components. Covers asset location, classification, lazy loading, above-the-fold exceptions, dynamic SVG exceptions, and refactoring rules.
---

# SKILL: External SVG Asset Enforcement (Svelte 5)

## Purpose

Enforce the use of external SVG assets instead of embedding SVG markup directly inside Svelte components.

This rule exists to:

- Reduce cognitive load for humans and LLMs.
- Keep components focused on UI and business logic.
- Improve maintainability.
- Prevent large SVG path definitions from polluting component source files.
- Enable asset reuse and browser caching.

---

## Enforcement Level

MANDATORY

This rule applies to:

- New code.
- Modified code.
- Refactored code.

Whenever a file is touched, any SVG violating these rules should be corrected if the change scope reasonably allows it.

---

## Primary Rule

SVG markup must not be embedded directly inside Svelte components.

Forbidden:

```svelte
<svg>
	<path d="M104.233..." />
</svg>
````

Allowed:

```svelte
<img src="/images/hero.svg" alt="" />
```

```svelte
import LogoSvg from '$lib/assets/logo.svg';
```

```svelte
<LogoSvg />
```

---

## Asset Location

When a project contains:

```text
frontend/static/
```

SVG files must be stored somewhere under:

```text
frontend/static/
```

using the most appropriate folder for the asset type.

Examples:

```text
frontend/static/icons/
frontend/static/images/
frontend/static/illustrations/
frontend/static/logos/
frontend/static/branding/
```

The exact subfolder depends on the asset purpose.

---

## Classification Rules

### Small UI Icons

Examples:

* edit
* delete
* save
* search
* close
* menu
* chevron
* arrow

Recommended locations:

```text
frontend/static/icons/
```

These assets may be:

* external SVG files
* icon libraries

Do not inline raw SVG markup unless explicitly required by dynamic rendering needs.

---

### Medium Assets

Examples:

* logos
* badges
* partner marks
* decorative graphics

Recommended locations:

```text
frontend/static/logos/
frontend/static/images/
frontend/static/branding/
```

Must remain external.

---

### Large Assets

Examples:

* hero illustrations
* landing page graphics
* marketing artwork
* complex diagrams
* exported Figma illustrations
* SVGs containing large path definitions

Recommended locations:

```text
frontend/static/illustrations/
frontend/static/images/
```

Must remain external.

Must never be embedded in component source.

---

## Lazy Loading Requirements

Large SVG assets should be loaded lazily whenever they are not required for immediate first paint.

Preferred:

```svelte
<img
	src="/illustrations/architecture.svg"
	loading="lazy"
	alt=""
/>
```

Examples that should typically be lazy-loaded:

* below-the-fold illustrations
* marketing graphics
* informational diagrams
* decorative artwork
* footer illustrations

---

## Above-the-Fold Exception

Do not lazy-load assets that are critical to the initial viewport.

Examples:

* primary company logo
* hero image visible immediately on page load
* critical branding assets

For these assets:

```svelte
<img
	src="/logos/company.svg"
	alt="Company"
/>
```

is acceptable.

---

## Dynamic SVG Exception

Inline SVG is allowed only when the SVG structure itself is dynamic.

Examples:

```svelte
<svg>
	<path d={generatedPath} />
</svg>
```

```svelte
<svg>
	<circle r={radius} />
</svg>
```

```svelte
<svg>
	{#each points as point}
		<circle ... />
	{/each}
</svg>
```

Typical use cases:

* charts
* graphs
* visualizations
* dynamically generated geometry
* SVG animations requiring direct DOM manipulation

Static SVG artwork does not qualify for this exception.

---

## Refactoring Rule

When encountering:

```svelte
<svg>
	...
</svg>
```

determine whether the SVG is:

* static
* reusable
* exported artwork

If yes:

1. Extract it into an external SVG file.
2. Move it into the appropriate asset directory.
3. Replace inline markup with an asset reference.
4. Apply lazy loading when appropriate.

---

## Cognitive Load Priority

Maintainability and readability take precedence over micro-optimizations.

If there is a choice between:

* embedding hundreds of SVG lines in a component

or

* storing the SVG as an external asset

always prefer the external asset.

The default assumption must be:

> Static SVGs belong in asset files, not inside Svelte component source code.


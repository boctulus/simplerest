---
name: i18n
description: Guide for internationalization in SimpleRest using gettext-based translations with .po/.mo files, locale structure, and helper functions.
---

# i18n Skill

SimpleRest uses **gettext** for internationalization with translation files in `app/Locale/`.

## Directory Structure

```
app/Locale/
├── es/
│   └── LC_MESSAGES/
│       ├── messages.po      # editable source
│       └── messages.mo      # compiled binary
├── en/
│   └── LC_MESSAGES/
│       ├── messages.po
│       └── messages.mo
└── ...
```

## Translation Domains

| Domain | Scope |
|--------|-------|
| `messages` | General application messages |
| `validator` | Validation error messages |
| `acl` | ACL system messages |

## Usage in Controllers / Views

```php
// Simple string
echo __('Welcome to SimpleRest');

// With placeholder
echo __('Hello %s!', $name);

// Echo directly
_e('Hello');

// From controller
$greeting = trans('greeting.morning');
```

## Validation i18n

```php
Translate::bind('validator');
// Validation error messages will use the 'validator' domain
```

## Creating Translation Files

1. Create `.po` file in the appropriate locale directory
2. Edit translations in the `.po` file
3. Compile to `.mo`:
   ```bash
   msgfmt app/Locale/es/LC_MESSAGES/messages.po -o app/Locale/es/LC_MESSAGES/messages.mo
   ```

## Locale Configuration

Set in `.env` or `config/config.php`. The locale is loaded during bootstrap in `app.php`.

## See Also

- [`docs/i18n.md`](../docs/i18n.md) — full reference
- [`docs/Validation.md`](../docs/Validation.md) — i18n validation messages
- `helpers` skill — `__()`, `_e()`, `trans()` helpers

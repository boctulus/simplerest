# Contributing to SimpleRest

Thank you for your interest in contributing to SimpleRest! This guide will help you get started.

---

## How to Contribute

### Reporting Bugs

Before creating a bug report:
1. Check the existing [issues](https://github.com/boctulus/simplerest/issues)
2. Ensure the bug is reproducible with the latest version

When creating a bug report, include:
- **Clear title and description**
- **Steps to reproduce** (code, commands, expected vs actual behavior)
- **Environment** (PHP version, OS, database)
- **Error messages** or stack traces

### Suggesting Features

Feature suggestions are welcome! Please include:
- **Use case**: What problem does this solve?
- **Proposed solution**: How should it work?
- **Alternatives considered**: Other approaches you've thought about

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Make your changes
4. **Write tests** for new functionality
5. Ensure tests pass: `vendor/bin/phpunit`
6. Commit with a clear message (see below)
7. Push and open a Pull Request

---

## Coding Standards

### General Principles
- **KISS** — Keep it simple
- **DRY** — Don't repeat yourself
- **SOLID** — Follow SOLID principles
- **Clean Code** — Readable, maintainable code

### PHP Style
- PSR-12 coding standard
- Meaningful variable and function names
- Comments for **why**, not **what**
- No inline business logic in routes/controllers

### Architecture
- Use the **modular architecture** — packages in `packages/`, modules in `app/Modules/`
- Follow existing patterns — don't introduce new patterns without discussion
- **Business logic ≠ routing** — use Middleware for cross-cutting concerns
- **No fallbacks** without prior approval

---

## Commit Messages

Follow this format:

```
Short summary of changes (max 72 chars)

- Bullet point details
- More details if needed

Pablo Bozzolo (boctulus)
Software Architect
```

Examples:
```
Fix $config undefined variable in test files

- Add Config::get() initialization in ApiCollectionsTest
- Same fix for ApiTest, ApiTrashCanTest, AuthTest

Pablo Bozzolo (boctulus)
Software Architect
```

---

## Testing

### Running Tests

```bash
# Full test suite
vendor/bin/phpunit

# Specific test file
vendor/bin/phpunit tests/ApiCollectionsTest.php

# Exclude refactor group
vendor/bin/phpunit --exclude-group refactor
```

### Writing Tests

- Tests must be **significant** and **general** — not just happy path
- Test files go in `tests/` (unit) or `tests/unit-tests/`
- Use the existing test structure as a template
- Tests that consume a database should use tables with `test_` prefix

---

## Development Workflow

### Before Starting
1. Read relevant docs in `docs/`
2. Check existing CLI commands: `php com help`
3. Understand the module/package you're working on

### During Development
- Make **incremental changes** — no big-bang rewrites
- Test before considering a task complete
- Save your prompt to `prompts/{yyyy-mm-dd hh-mm} {git-hash}.txt`

### After Completion
- Run tests
- Update documentation if applicable
- Ask: *"Can documentation be created or updated?"*

---

## Documentation

New features **must** be documented:

| Type | Location |
|------|----------|
| Commands | `docs/commands/` |
| Components | `docs/components/` |
| Modules | `docs/modules/` |
| Packages | `docs/packages/` |
| Issues | `docs/issues/` |
| Major changes | `docs/CHANGELOG-*.md` |

---

## Security

- **Never** commit credentials, API keys, or secrets
- **Never** delete production/master tables without explicit approval
- Test data may be deleted, but source-of-truth data must be protected
- Any destructive operation requires explicit approval in the PR

---

## Questions?

If you're unsure about anything, open an issue or ask in a discussion. It's better to ask than to implement something that needs to be reverted.

---

**Thank you for contributing!** 🎉

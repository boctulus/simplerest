# RFC — Modular, extensible and lazy-loaded SimpleRest CLI

Status: **design accepted in principle; implementation deferred until after Composer P0**  
Date: 2026-10-22  
Consumer blocker: [GoProp Composer P0](simplerest-composer-migration-p0.md)  
Scope: framework CLI conventions and extension contracts, not an immediate rewrite.

## Goals and principles

1. Preserve SimpleRest's **one command per PHP file, grouped by directory** convention:
   `app/Commands/users/ListCommand.php` maps to `php com users list`.
2. Allow a consumer to add `users export` even if the standard `users` group is supplied by a future optional Composer package.
3. Permit **explicit replacement** of `users migrate` when the whole implementation differs; never silently shadow one command with another.
4. Prefer **small typed extension contracts** for application-specific variations within a generic command (such as extra user migration fields), avoiding forks of generic commands.
5. Prevent PHP class-name collisions: use distinct PSR-4 namespaces for packaged and application commands, independently of the CLI group name.
6. Separate command discovery metadata from command instantiation. Commands should be loaded **only when invoked**; HTTP startup must not discover/instantiate CLI commands.
7. Keep the framework core minimal and separate optional tooling by sensible, independently versionable **domains**, not one Composer package per individual command.
8. Preserve stable CLI invocation/help/exit-code contracts and benchmark performance before and after any change.

## Current verified behavior and gaps

- `Boctulus\Simplerest\Core\Libs\CommandRegistry` searches `COMMANDS_PATH`, extra `config/commands.php['paths']` entries and opt-in `['packages']` beneath project-local `PACKAGES_PATH/vendor/name/src/Commands`.
- It does not generically discover Composer-installed `vendor/vendor-name/package-name` commands.
- Discovery scans `*Command.php`, requires files, instantiates command classes and keys registrations by `group:command`, replacing an earlier entry if a later provider claims the same key.
- Several existing command classes use global class names such as `ListCommand`; those are collision-prone as package count increases.
- GoProp has a custom `com` entrypoint, `app/Commands/` and `config/commands.php`; these remain project-owned during P0.
- The current `.packinclude` of SimpleRest includes selected CLI folders (file/migrations/module/mysql/router/sql/system), but this does not prove those classes will be discovered by a Composer consumer. Inspect the actual installable artifact.

## Logical identity and physical layout

Command identity is the normalized `(group, command)` pair. Directory layout is a developer convention, **not** the permanent identity. Preserve the readable layout for project commands while allowing external providers to contribute a manifest:

```text
goprop/app/Commands/users/ExportCommand.php
vendor/boctulus/simplerest-cli-users/src/Commands/users/ListCommand.php
vendor/boctulus/simplerest-cli-users/src/Commands/users/MigrateCommand.php
```

These may yield `users export`, `users list` and `users migrate` without copying external files into GoProp.

Use PSR-4 namespaces such as `GoProp\Commands\Users\ExportCommand` and `Boctulus\SimplerestCliUsers\Commands\Users\ListCommand`, not globally defined `ListCommand` classes.

## Three separate customization mechanisms

### A. Add a command

A new `app/Commands/users/ExportCommand.php` registers `users export`. No base package change. Group help merges commands from the enabled providers without repeated aliases.

### B. Replace a command explicitly

When an entire application implementation is intentionally different, configure an override mapping. Treat the following as a **proposed** future contract, not currently valid configuration:

```php
return [
    'command_overrides' => [
        'users migrate' => \GoProp\Commands\Users\MigrateCommand::class,
    ],
];
```

The registry must verify that the declared target exists, implements the command interface, matches the expected logical signature, and produces deterministic help/dispatch. Record original provider and override provenance in diagnostics.

**Collision policy:** without an explicit override, duplicate `group:command` keys are a validation error. Local commands are not implicitly entitled to shadow installed commands. For compatibility a migration period may warn for known legacy conflicts, but a production build must fail on unresolved ambiguity.

### C. Extend a generic command through a typed policy/hook

A consumer that adds fields to a user migration should not duplicate the entire generic command. The generic operation delegates a precisely defined transformation/validation step to an optional policy:

```php
interface UserMigrationTransformer
{
    public function transform(array $row, UserMigrationContext $context): array;
}
```

The project provides its own implementation and injects/registers it through configuration, a factory or a small DI adapter. The resulting row is then validated by the canonical service and persisted only through authorized models/services.

Hooks must declare ordering, error handling, validation boundaries, idempotency and transaction semantics. Distinguish **transformers** (which can change input), **validators** (which can reject), and **observers** (which cannot mutate committed state). Do not introduce unrestricted arbitrary callbacks for every internal step.

Generic PHP CLI events such as command start/finish may be added for telemetry. They must never expose secrets in payloads and cannot replace domain-specific extension points.

## Database migrations are a separate concern

A generic `create_users` migration and a GoProp-specific `add_organization_fields_to_users` migration should coexist without editing a package migration. Maintain a deterministic migration history and ordering, rollback/backward-compatibility policy, MySQL/PostgreSQL compatibility checks and test fixtures. A CLI migration **command override** and an application's **schema extension migration** solve different problems; do not conflate them.

Do not let an extension write directly to database tables, bypassing models/services or existing guards.

## Composer packaging strategy

- **P0:** `boctulus/simplerest` provides a consumable canonical runtime core; GoProp keeps all existing application commands. No command-domain packages are a prerequisite.
- **P1:** if useful, extract generic reusable CLI tooling into one or a few optional packages. Provisional domains: command runtime, code generation, user administration, database/migration tooling, development/test utilities. Actual boundaries require an inventory of coupling, dependencies, consumers and release cadence.
- **P2:** optionally create narrower domain packages only when independent releases, security permissions or heavyweight optional dependencies justify them. Avoid packaging one trivial command per distribution.
- Application-specific `goprop-users` is **optional**. Do not create it merely to eliminate a collision: with no generic command package installed, GoProp's existing local `users` implementation is already isolated.
- External command inclusion should be **opt-in** and explicit, not inferred from every package in `vendor/`.

## Proposed registry manifest and lazy loading

Use a deterministic registry manifest with logical command key, class, provider, argument/help metadata, aliases and priority/override declaration. Build/validate the manifest at deployment or development cache-refresh time, not by traversing every package on each CLI invocation.

At dispatch:
1. Load only the manifest and command metadata.
2. Resolve `group`, `command`, aliases and explicit overrides.
3. Load/instantiate **only the requested command** (and its injected policies).
4. Validate args and execute. Group help can render from manifest metadata without instantiating every command.
5. Expose a diagnostic `commands doctor` or equivalent to show source/provider, overridden commands and collisions.

Do not require a heavyweight runtime container. Small factories and explicit contracts are sufficient.

The implementation must consider current global command class names, `require_once` side effects, autoload-dev vs autoload, legacy help, aliases and path casing in Linux. Provide a compatibility shim while migrating command metadata.

## Performance and quality gates

Composer itself is not evidence of slow startup. Measure actual **loaded files, class initialization, service provider work, directory scanning, disk I/O and PHP autoload**.

Record baseline and candidate cold/warm CLI help and dispatch startup, representative HTTP requests, median/p95 wall time, memory usage and class/file counts with equivalent PHP, OPcache, filesystem and cache state. Define material-regression thresholds from the baseline before accepting a CLI loader rewrite; do not invent numeric targets or claim SimpleRest is faster than another framework without a reproducible comparison.

Tests should cover:
- Group help combining internal and explicitly enabled external commands.
- Same file basenames in different namespaces and distinct logical command keys.
- Duplicate command key failure and explicit override success.
- Cross-group aliases and help parity.
- Extension transformer/validator behavior and unauthorized mutation prevention.
- Migration ordering and rollback with MySQL and PostgreSQL where applicable.
- No CLI discovery on HTTP request; only selected CLI command instantiated.
- `composer install --no-dev --optimize-autoloader` and case-sensitive Linux execution.

## Implementation order

1. Finish the [GoProp Composer cutover](simplerest-composer-migration-p0.md) with local application commands unchanged.
2. Inventory the SimpleRest and GoProp CLI commands, collision pairs, use cases and package dependencies.
3. Define stable interfaces/metadata and explicit conflict resolution; write contract tests.
4. Introduce optional registration for external Composer package commands.
5. Implement typed extensions only for commands with concrete duplication, beginning with user migration if needed.
6. Replace eager directory-wide class loading with lazy manifest dispatch.
7. Extract optional domain packages incrementally; compare cold/warm startup after every stage.
8. Encourage students to work on framework contracts in SimpleRest and validate them against GoProp via Composer rather than copying source.

## Acceptance

- [ ] Application commands remain unchanged through initial Composer migration.
- [ ] Provider registration is opt-in and has reproducible semantics.
- [ ] No duplicate logical command is silently replaced.
- [ ] Namespace collisions are eliminated.
- [ ] Generic command extension does not require editing a file in `vendor/`.
- [ ] Group help remains coherent and compatibility tests pass.
- [ ] CLI is lazily loaded and HTTP boot avoids CLI discovery.
- [ ] Performance impact is evidenced, not assumed.
- [ ] Domain package boundaries follow demonstrated reuse rather than arbitrary granularity.

## References in repository

- `src/framework/Libs/CommandRegistry.php`
- `src/framework/Commands/BaseCommand.php`
- `com`
- `config/commands.php`
- `app/Commands/`
- `docs/architecture/simplerest-composer-migration-p0.md`

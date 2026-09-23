# Schema descriptors

This page documents the checked-in schema descriptor and generator paths from source. No schema generator or DDL operation was run in this audit.

## Descriptor format and lookup

The generated [schema template](../../src/framework/Templates/Schema.php) emits a PHP class implementing [`ISchema`](../../src/framework/Interfaces/ISchema.php). Its static `get()` returns metadata such as `table_name`, `id_name`, `fields`, `attr_types`, `rules`, primary-key data, and relationship arrays. The generated class is not the standalone array literal shown in the quarantined guide.

[`Model::__construct()`](../../src/framework/Model.php) accepts a schema class and calls its `get()` method. [`DBRels::getSchemaName()` and `getSchemaPath()`](../../src/framework/Libs/DBRels.php) resolve a class and file path using the configured namespace plus the current connection's default ID or tenant group. [`SCHEMA_PATH`](../../src/framework/Constants.php) points into the application schema directory. A portable copy-and-run path has not been verified across operating systems.

## Generating from a database table

`MakeSchemaCommand::execute()` delegates to [`BaseMakeCommand::schema()`](../../app/Commands/make/BaseMakeCommand.php). That path checks for an existing table, reads its columns and other metadata, fills the schema template, and writes the descriptor. It does not create the SQL table or a migration.

The introspection uses MySQL-oriented SQL, including `SHOW COLUMNS` and `information_schema` column metadata. [`Schema::hasTable()`](../../src/framework/Libs/Schema.php) has SQLite and MySQL branches, but that does not make the later metadata reads portable. An isolated in-memory SQLite PDO probe rejected `SHOW COLUMNS`; PostgreSQL and other drivers were not run.

The schema generator passes `protected = false` into the file writer. In the current source this allows an existing schema file to be replaced without `--force`. No generator was run against the working tree.

## Scope still under audit

The framework also has a [`Schema` DDL builder](../../src/framework/Libs/Schema.php), separate from generated `ISchema` metadata. Its individual column methods, emitted SQL by driver, relation and pivot inference, and runtime validation contract are not documented here. [`SchemaBugsTest`](../../unit-tests/schema/SchemaBugsTest.php) contains SQL-generation assertions, but it was inspected only; the configured [PHPUnit suite](../../phpunit.xml) does not include `unit-tests/`.

See the [claim-level schema and automatic API audit](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api), the [database index](README.md), and the [pending legacy schema guide](../audit/pending/framework/Schemas.md).

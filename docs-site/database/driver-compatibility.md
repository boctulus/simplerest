---
title: Compatibilidad de drivers de base de datos
source:
  - docs/database/driver-compatibility.md
  - docs/database/schemas.md
  - docs/database/schema-builder.md
  - docs/database/relations.md
  - docs/database/transactions.md
source_revision: e0a3dc3f5843be258eed7d5cc1edec13031d5f87
status: partial
---

# Compatibilidad de drivers de base de datos

Una rama PDO no demuestra que todas las consultas, joins, esquemas o migraciones funcionen con ese motor. Esta auditoría no abrió conexiones ni ejecutó consultas.

## Ramas de conexión

`DB::getConnection()` construye PDO para `mysql`, `sqlite`, `pgsql` y `sqlsrv`. Al construir el DSN mapea `mariadb` a `mysql`, `postgres` a `pgsql` y `mssql` a `sqlsrv`. `DB::driver()` devuelve el valor configurado original; algunas ramas posteriores comparan ese valor exacto. Por eso una alias aceptada para crear PDO puede no activar la conversión de floats de PostgreSQL, los savepoints o las expresiones de agregación de subrecursos.

## Límites por operación

| Área | Evidencia de fuente | Límite |
| --- | --- | --- |
| Conexión | Ramas PDO para cuatro drivers y tres aliases de construcción | No se comprobó extensión PDO ni conectividad |
| Transacciones | Savepoints con los valores exactos `mysql`, `sqlite` y `pgsql` | Sin transacción ejecutada; el rollback anidado puede revertir toda la transacción |
| Descriptores | Lecturas `SHOW COLUMNS` e `information_schema` | Una prueba aislada SQLite rechazó `SHOW COLUMNS`; no se ejecutó el generador PostgreSQL |
| DDL | Backticks, sintaxis y metadata SQL orientadas a MySQL | No es una promesa portable; no se ejecutó DDL |
| Subrecursos | Expresiones de agregación diferenciadas en fuente para MySQL/MariaDB, SQLite y PostgreSQL | El SQL también usa otras funciones y no se ejecutó por motor |
| Lista de tablas | Rutas específicas para MySQL y PostgreSQL | Otros drivers reciben una excepción de método no soportado |

Los detalles están separados en las guías de [base de datos y descriptores](/database#descriptores-de-esquema), [constructor DDL](/database/schema-builder), [relaciones](/database/relations) y [transacciones](/database/transactions). No existe aquí una matriz validada para todos los métodos.

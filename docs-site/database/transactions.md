---
title: Transacciones de base de datos
source:
  - docs/database/transactions.md
  - docs/audit/README.md
source_revision: e0a3dc3f5843be258eed7d5cc1edec13031d5f87
status: partial
---

# Transacciones de base de datos

La fuente mantiene niveles de transacción por conexión seleccionada. La transacción exterior activa el modo de excepciones de PDO y llama a `PDO::beginTransaction()`. Mientras exista una transacción activa, el código protege la afinidad de conexión: no permite cambiar a otra conexión ni cerrar la activa. No coordina transacciones distribuidas.

## Anidamiento

Los savepoints se usan cuando `DB::driver()` devuelve exactamente `mysql`, `sqlite` o `pgsql`. El inicio anidado crea `LEVEL_n`; el commit intenta liberar ese savepoint y reduce el nivel. Si no se usa savepoint, el commit anidado sólo reduce el nivel.

El rollback anidado revierte al savepoint y trata de liberarlo. Si falla ese rollback, la fuente intenta revertir toda la transacción. Cuando el valor del driver no activa savepoints, un rollback anidado también revierte la transacción completa.

Los aliases `mariadb`, `postgres` y `mssql` se normalizan al construir PDO, pero `DB::driver()` conserva el texto configurado. Por eso no necesariamente activan la rama de savepoints. Consulta [compatibilidad por driver](/database/driver-compatibility).

## `DB::transaction()`

El helper inicia la transacción antes de entrar a su `try`. Si el callback retorna, hace commit y devuelve ese resultado. Si el callback o el commit lanza un `Throwable`, intenta rollback, oculta un error de rollback y vuelve a lanzar la excepción capturada. Un error al iniciar queda fuera de ese `catch`.

`DB_TransactionTest.php` contiene casos SQLite en memoria para commit, rollback, anidamiento y afinidad de conexión. `PGSQLQueryBuilderTest.php` contiene casos PostgreSQL. Se inspeccionaron, pero no se ejecutaron; no prueban un resultado actual de base de datos.

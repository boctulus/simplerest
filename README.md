# SimpleRest Framework

> **The fastest PHP framework that speaks your logic.**
> 
> Laravel-like syntax. Zero-magic philosophy. Bootstrap in 3-10ms.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-7.4%20--%208.4-blue.svg)](https://php.net)
[![Tests](https://github.com/boctulus/simplerest/actions/workflows/tests.yml/badge.svg)](https://github.com/boctulus/simplerest/actions/workflows/tests.yml)

---

## What is SimpleRest?

SimpleRest is a **modular PHP framework** designed for building REST APIs and web applications with minimal boilerplate. It gives you the developer experience of Laravel — routing, query builder, models, auth, CLI — without the overhead of heavy dependency injection containers, PSR abstractions, or ORM hydration.

**Philosophy**: *Performance through simplicity. Eliminate every unnecessary abstraction layer.*

---

## Quick Comparison

| Metric | SimpleRest | Laravel |
|--------|-----------|---------|
| **Bootstrap time** | 3-10 ms | 300-500 ms |
| **Data return type** | Arrays (no ORM objects) | Eloquent objects |
| **DI Container** | Optional, minimal | Heavy, reflection-based |
| **Auto REST endpoints** | ✅ Zero config | ❌ Manual |
| **Built-in ACL** | ✅ Hierarchical, granular | Via packages |
| **Composer required** | No (works standalone) | Yes |
| **PHP versions** | 7.4 – 8.4 | 8.1+ |

---

## Killer Features

### 🔥 Automatic REST Endpoints
Create a table, generate a schema file — **your full CRUD API is ready**. No controller code needed.

```bash
php com make schema products
# Done. GET /api/products, POST, PUT, DELETE — all working.
```

### 🎯 Advanced Filtering (built-in)
```bash
# Price >= 10 AND name contains "Widget"
/api/products?price[gteq]=10&name[contains]=Widget

# Only specific fields, paginated
/api/products?fields=id,name&limit=10&offset=20

# Aggregation
/api/products?aggregate=count(id),avg(price),max(price)

# Include related data
/api/products?include=categories,reviews
```

### 🔐 JWT Authentication
Out-of-the-box login, registration, password reset, token refresh.

### 🛡️ Fine-Grained ACL
Hierarchical roles with inheritance, granular permissions, user-level overrides, and folder-based sharing. More sophisticated than most dedicated ACL packages.

### 🤝 AutoJoins
Relationships are **inferred automatically** from foreign keys or naming conventions. No explicit model declarations needed.

### ⚡ Query Builder
Laravel-compatible fluent syntax that returns arrays instead of objects — eliminating ORM hydration overhead.

```php
$users = DB::table('users')
    ->where('active', 1)
    ->where('role', 'admin')
    ->orderBy('name')
    ->limit(10)
    ->get();
```

### 🖥️ Powerful CLI System
12 built-in commands, easy extensibility:

```bash
php com make controller|model|schema|middleware|command|package Name
php com sql list-databases
php com sql find "SELECT * FROM users"
php com help
```

---

## Installation

```bash
git clone https://github.com/boctulus/simplerest.git
cd simplerest
composer install
cp .env.example .env
# Edit .env with your database credentials
```

**Full tutorial**: [docs/QuickStart.md](docs/QuickStart.md)

---

## Architecture

```
simplerest/
├── src/framework/    # Framework Core (reusable library)
│   ├── Api/          # API controllers
│   ├── Libs/         # 101 utility classes
│   ├── Handlers/     # Request processing pipeline
│   ├── Helpers/      # Global helpers
│   ├── Traits/       # Reusable traits
│   └── ...
├── app/              # Application code (playground/dogfooding)
├── packages/         # Local packages
├── config/           # Configuration
└── docs/             # Documentation
```

The framework core (`src/framework/`) is **completely separate** from application code, making it usable as a standalone library.

---

## Documentation

| Topic | Link |
|-------|------|
| **Quick Start** (5 min) | [docs/QuickStart.md](docs/QuickStart.md) |
| Routing | [docs/Routing.md](docs/Routing.md) |
| Query Builder | [docs/QueryBuilder.md](docs/QueryBuilder.md) |
| ORM / Models | [docs/ORM.md](docs/ORM.md) |
| Middleware | [docs/Middlewares.md](docs/Middlewares.md) |
| ACL | [docs/ACL.md](docs/ACL.md) |
| CLI Commands | [docs/CommandLine.md](docs/CommandLine.md) |
| API Client | [docs/ApiClient.md](docs/ApiClient.md) |
| Philosophy | [docs/SimpleRest-philosophy.md](docs/SimpleRest-philosophy.md) |
| PSR Compliance | [docs/PSR-SUMMARY.md](docs/PSR-SUMMARY.md) |

---

## Multi-Database Support

MySQL, PostgreSQL, SQLite, SQL Server, Oracle, Firebird, DB2, Informix, Sybase — all supported via adapters.

---

## Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

## License

SimpleRest is open-sourced under the MIT License. See [LICENSE](LICENSE) for details.

---

**Author**: [Pablo Bozzolo](https://github.com/boctulus) — Software Architect

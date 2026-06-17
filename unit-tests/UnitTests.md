# Pruebas Unitarias — SimpleRest

## Stack de Testing

| Herramienta | Propósito | Estado |
|-------------|-----------|--------|
| **PHPUnit** | Tests unitarios del framework | ⚠️ 228 tests: ~55% pasan |
| **Playwright** | Tests E2E / UI | ✅ Preferido para frontend |
| **Puppeteer** | Tests E2E alternativo | ✅ Disponible |
| **Selenium** | Tests E2E (Python) | ✅ Disponible |

---

## PHPUnit

### Configuración

Archivo: `phpunit.xml` en la raíz del proyecto.

```bash
# Ejecutar todos los tests
./vendor/bin/phpunit

# Ejecutar un archivo específico
./vendor/bin/phpunit tests/unit-tests/ApiCollectionsTest.php

# Con cobertura
./vendor/bin/phpunit --coverage-html reports/unit-tests/
```

### Estructura de Tests

```
tests/
├── unit-tests/
│   ├── ApiCollectionsTest.php
│   ├── ApiTest.php
│   ├── ApiTrashCanTest.php
│   └── AuthTest.php
├── PSR/
│   ├── ResponseAdapterTest.php
│   ├── ServerRequestAdapterTest.php
│   └── StreamAdapterTest.php
```

### Traits de Testing

Disponibles en `src/framework/Traits/`:

```php
use UnitTestCaseRunAllTrait;  # Ejecutar todos los tests
use UnitTestCaseSQLTrait;     # Helpers para tests de BD
```

### Reports

Los reportes de cobertura se generan en:

```
reports/unit-tests/
reports/automation/
```

---

## Testing E2E con Playwright

```bash
# Instalar dependencias (Node.js)
npm install @playwright/test
npx playwright install

# Ejecutar tests
npx playwright test
```

Los scripts de automatización se encuentran en:

```
web-automation/
automation/
```

---

## Estado Actual de los Tests

| Suite | Tests | Estado |
|-------|-------|--------|
| PSR-7 ResponseAdapter | 79 tests / 168 assertions | ✅ Pasan |
| PSR-7 ServerRequestAdapter | — | ✅ Pasan |
| PSR-7 StreamAdapter | — | ✅ Pasan |
| ApiCollections | — | ✅ Pasan |
| ApiTrashCan | — | 🔴 Bug conocido |
| ApiTest | — | 🔴 En progreso |
| AuthTest | — | 🔴 $config undefined |

**Problemas conocidos:**
- `$config` variable undefined en varios test files (error de bootstrap)
- `parse_url(null)` y `rtrim(null)` causan PHP warnings en PHP 8.x
- phpunit.xml tiene cobertura mal configurada (apunta a friendlypos-web en vez de src/framework)

---

## Orden de Testing (Recomendado)

1. **Backend/API** (curl directo o PHPUnit)
2. **UI** (Playwright)

Nunca al revés.

---

## Ver También

- [`CommandLine.md`](./CommandLine.md) — comando `php com test`
- [`release-status.md`](./release-status.md) — estado general del proyecto

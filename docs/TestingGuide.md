# Guía de Testing — SimpleRest

## Stack

| Herramienta | Propósito | Estado |
|-------------|-----------|--------|
| **PHPUnit** (v10) | Tests unitarios | 228 tests, ~55% pasan |
| **Playwright** | Tests E2E frontend | ✅ Preferido |
| **Puppeteer** | Tests E2E alternativo | ✅ Disponible |
| **Selenium** | Tests Python | ✅ Disponible |

## Orden de Testing (Regla)

1. **Backend/API** primero (curl, PHPUnit)
2. **UI** después (Playwright)
3. Nunca al revés

## PHPUnit

```bash
# Todos los tests
./vendor/bin/phpunit

# Archivo específico
./vendor/bin/phpunit tests/unit-tests/ApiCollectionsTest.php

# Con cobertura
./vendor/bin/phpunit --coverage-html reports/unit-tests/
```

### Tests PSR-7

```bash
./vendor/bin/phpunit tests/PSR/
```

79 tests / 168 assertions — todos pasan.

## Playwright

```bash
cd web-automation/
npm install
npx playwright test
```

## CLI Testing

```bash
php com test echo "hello"
php com test info
php com test calc 2 3
```

## Reports

```
reports/unit-tests/     → Cobertura PHPUnit
reports/automation/     → Tests E2E
```

## Scripts Disponibles

```bash
php com test echo     # Prueba de eco
php com test greet    # Prueba de saludo
php com test calc     # Prueba de cálculo
php com test info     # Info del sistema
```

## Ver También

- [`unit-tests-pruebas-unitarias.md`](./unit-tests-pruebas-unitarias.md) — guía de tests unitarios
- [`release-status.md`](./release-status.md) — estado actual de los tests

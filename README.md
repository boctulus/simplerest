# SimpleRest Framework

Framework PHP modular y extensible con soporte PSR.

**Versión**: 0.9.0
**Type**: Library
**License**: MIT

---

## 🏗️ Arquitectura

SimpleRest sigue una **arquitectura desacoplada** donde el Framework Core está completamente separado del código de aplicación:

```
simplerest/
├─ src/         # Framework Core (biblioteca reutilizable)
├─ app/         # Application Code (playground/dogfooding)
├─ modules/     # Módulos opcionales
├─ examples/    # Demos y ejemplos
└─ packages/    # Packages locales
```

**Documentación completa**: [`docs/Framework-Architecture.md`](./docs/Framework-Architecture.md)

---

## PSR Compliance

SimpleRest ahora soporta estándares PSR para mejorar la interoperabilidad con el ecosistema PHP moderno.

### Estado Actual

- ✅ **PSR-7**: HTTP Message Interfaces (via adapters + métodos nativos)
- ✅ **Immutability**: Métodos inmutables `with*()` en Request y Response
- 📋 **PSR-17**: HTTP Factories (planeado)
- 📋 **PSR-15**: HTTP Server Request Handlers (planeado)

**Compliance**: 95% PSR-7 compatible

### Documentación PSR

Para información detallada sobre la implementación PSR:

- **Resumen General**: [`docs/PSR-SUMMARY.md`](./docs/PSR-SUMMARY.md)
- **Guía PSR-7**: [`docs/PSR-7.md`](./docs/PSR-7.md)
- **Métodos Inmutables**: [`docs/ImmutableMethods.md`](./docs/ImmutableMethods.md)
- **Changelog PSR**: [`docs/CHANGELOG-PSR.md`](./docs/CHANGELOG-PSR.md)

### Uso Rápido

```php
// Métodos inmutables (recomendado para nuevo código)
$response = Response::getInstance()
    ->withStatus(201)
    ->withJson(['id' => $newId]);

// PSR-7 via adapters (para interoperabilidad)
$psr7Request = psr7_request();
$psr7Response = psr7_json(['success' => true], 200);
```

---

## 📚 Documentación

### Arquitectura y Estructura
- **Arquitectura del Framework**: [`docs/Framework-Architecture.md`](./docs/Framework-Architecture.md)
- **Guía de Migración v0.9**: [`docs/MIGRATION-v0.9.md`](./docs/MIGRATION-v0.9.md)
- **Changelog**: [`docs/CHANGELOG.md`](./docs/CHANGELOG.md)
- **Core Directives**: [`docs/core-directives.md`](./docs/core-directives.md)

### Desarrollo
- **Comandos CLI**: [`docs/CommandLine.md`](./docs/CommandLine.md)
- **ApiClient**: [`docs/ApiClient.md`](./docs/ApiClient.md)
- **Testing**: [`docs/unit-tests-pruebas-unitarias.md`](./docs/unit-tests-pruebas-unitarias.md)
- **ORM**: [`docs/ORM.md`](./docs/ORM.md)
- **Query Builder**: [`docs/QueryBuilder.md`](./docs/QueryBuilder.md)
- **Routing**: [`docs/Routing.md`](./docs/Routing.md)

### PSR Compliance
- **Resumen General**: [`docs/PSR-SUMMARY.md`](./docs/PSR-SUMMARY.md)
- **Guía PSR-7**: [`docs/PSR-7.md`](./docs/PSR-7.md)
- **Métodos Inmutables**: [`docs/ImmutableMethods.md`](./docs/ImmutableMethods.md)
- **Changelog PSR**: [`docs/CHANGELOG-PSR.md`](./docs/CHANGELOG-PSR.md)

### Packages y Módulos
- **Packages y Módulos**: [`docs/Packages and Modules.md`](./docs/Packages%20and%20Modules.md)
- **Module Provider**: [`docs/ModuleProvider.md`](./docs/ModuleProvider.md)

---

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
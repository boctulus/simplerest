# SimpleRest Framework

Framework PHP modular y extensible con soporte PSR.

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

## Documentación

- **Comandos CLI**: [`docs/CommandLine.md`](./docs/CommandLine.md)
- **ApiClient**: [`docs/ApiClient.md`](./docs/ApiClient.md)
- **Testing**: [`docs/unit-tests-pruebas-unitarias.md`](./docs/unit-tests-pruebas-unitarias.md)
- **Core Directives**: [`docs/core-directives.md`](./docs/core-directives.md)

---

**Autor**: Pablo Bozzolo (boctulus)
**Software Architect**
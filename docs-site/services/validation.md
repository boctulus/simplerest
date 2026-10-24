---
title: Validación de datos en el servidor
source:
  - docs/services/validation.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# Validación de datos en el servidor

`Validator` aplica reglas PHP a un array y devuelve `true` o `false`. No valida campos del navegador ni enlaza errores automáticamente con [controles HTML](/services/forms).

## Uso y reglas

```php
use Boctulus\Simplerest\Core\Libs\ValidationRules;
use Boctulus\Simplerest\Core\Libs\Validator;

$rules = (new ValidationRules())
    ->field('email')->type('email')->required()
    ->field('age')->type('int')->min(18)->max(120)
    ->getRules();

$validator = new Validator();
if (!$validator->validate($data, $rules)) {
    $errors = $validator->getErrors();
}
```

`ValidationRules` ofrece `field`, `type`, `required`, `min`, `max`, `between`, `notBetween`, `in`, `notIn` y `getRules`. Las reglas directas también aceptan `set`, `not_in`, longitudes, `structure` y mensajes.

El dispatcher mapea tipos como boolean, integer, float, number, string, email, URL, MAC, dominio, fecha, hora, datetime, JSON y object, además de variantes y aliases. `regex:<patrón>` y `decimal(total,decimales)` usan ramas especiales. `either` aparece en la tabla, pero el método requiere opciones que `isType()` no pasa; no se debe asumir que funcione. `getErrors()` informa por campo; `getWarnings()` expone advertencias. El validador no normaliza ni corrige valores.

## Reglas y semánticas

- `required` rechaza ausencias o valores vacíos cuando está activo; `setRequired(false)` desactiva ese rechazo.
- `skipBoundsChecks(true)` omite límites, pero no tipos, listas ni unicidad.
- `ignoreFields([...])` ignora un campo presente; la detección de ausencia ocurre antes de esa rama.
- Las listas `$fillables` y `$not_fillables` rechazan campos, pero no filtran el array.
- `setUniques($fields, $table)` consulta `exists()`; requiere base de datos, no excluye automáticamente el registro actual y no sustituye un índice único.
- Los límites dependen de nombres de tipos literales; un alias puede pasar el tipo y no entrar en la misma rama de `min` o `max`. `in`/`not_in` usan comparación no estricta.
- `validate($data, null)` retorna `true`; `validate($data, [])` lanza `InvalidArgumentException`.
- Si una regla `array` recibe un escalar y además una longitud, el código llega a `count()` después del error de tipo; esa combinación no está demostrada como segura.

El constructor enlaza el dominio `validator` de `Translate`, y el core no declara el paquete i18n como dependencia. La interfaz `IValidator` es más estrecha que la clase concreta y sólo exige `getErrors()`.

Se inspeccionaron las clases y tests de `unit-tests/validator/`, sin ejecutarlos. No se probó unicidad contra una base real ni sincronización con reglas del cliente.

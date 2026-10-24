# Validación de datos en el servidor

`Validator` aplica reglas PHP a un array de datos y devuelve `true` o `false`. No es un validador de campos en el navegador ni enlaza por sí solo mensajes con los controles generados por [HTML y formularios](forms.md).

## Uso y reglas

Las reglas pueden escribirse como arrays o generarse con `ValidationRules`:

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

`ValidationRules` ofrece `field`, `type`, `required`, `min`, `max`, `between`, `notBetween`, `in`, `notIn` y `getRules`. Las reglas escritas directamente también pueden declarar `set`, `not_in`, longitudes de arrays, `structure` y mensajes por regla.

Los tipos mapeados incluyen boolean, integer, float, number, string, alpha, variantes con guiones/espacios y variantes UTF-8, email, URL, MAC, dominio, fecha, hora, datetime, JSON y object. Hay aliases como `bool`, `int`, `num`, `str` y `timestamp`; `regex:<patrón>` y `decimal(total,decimales)` tienen ramas especiales. `either` aparece en la tabla de despacho, pero su método requiere un argumento de opciones que `isType()` no pasa; esa ruta no está lista para asumirse funcional. Una clase o regla con un nombre parecido no garantiza que el tipo esté registrado.

`getErrors()` entrega errores por campo, con tipo y detalle; `getWarnings()` consulta advertencias como aceptar un array asociativo en una regla `string`. El validador no corrige ni normaliza los valores de entrada.

## Requeridos, límites y listas permitidas

- `required` rechaza un campo ausente o vacío cuando la regla lo marca como requerido. `setRequired(false)` desactiva el rechazo por ausencia/vacío para esa instancia.
- `skipBoundsChecks(true)` omite controles de `min`, `max`, `between` y `not_between`; no omite tipos, listas `in` ni unicidad.
- `ignoreFields([...])` salta la validación de los campos indicados cuando hay un valor presente; la comprobación de campo ausente ocurre antes de esa rama.
- `validate($data, $rules, $fillables, $not_fillables)` puede rechazar campos no incluidos en una lista o presentes en una lista prohibida. Estas opciones no limpian ni filtran el array.
- `setUniques($fields, $table)` hace consultas `exists()` para los campos presentes y necesita acceso a base de datos. Es una comprobación previa, no una restricción única atómica ni un sustituto de un índice de base de datos.

Las reglas de array pueden indicar `len`, `min_len` o `max_len`; las estructuras describen objetos/arrays anidados. Si un `type: array` recibe un escalar y además una regla de longitud, la implementación sigue hasta `count()` después de registrar el tipo, por lo que esa combinación no está demostrada como segura.

## Semánticas que conviene conocer

- `validate($data, null)` retorna `true` sin aplicar reglas; `validate($data, [])` lanza `InvalidArgumentException`.
- Las reglas de texto/número/fecha condicionan `min` y `max` con listas literales de tipos. Que un alias supere el chequeo de tipo no garantiza que entre en la misma rama de límites.
- `in` y `not_in` usan `in_array()` sin modo estricto.
- La comprobación `unique` no excluye automáticamente el registro actual en una actualización y puede competir con una escritura concurrente.
- El constructor de `Validator` enlaza el dominio `validator` de `Translate`; la referencia de [i18n](i18n.md) describe la dependencia del paquete de traducción.

La interfaz `IValidator` declara una firma más estrecha que la implementación concreta para `$rules`, y solo exige `getErrors()`. Consulta la clase concreta para `getWarnings()` y los métodos de configuración.

## Alcance de verificación

Se inspeccionaron `Validator`, `ValidationRules`, `IValidator` y tests en `unit-tests/validator/`; no se ejecutaron. La lectura no demuestra comportamiento de formularios en navegador, una consulta de unicidad contra una base real ni alineación automática con reglas del cliente.

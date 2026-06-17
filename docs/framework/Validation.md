# Validación — SimpleRest

## Clase Validador

**Archivo**: `src/framework/Libs/Validator.php` (632 líneas)  
**Interfaz**: `IValidator`

Validador de campos de formulario con soporte i18n, reglas personalizadas y verificación de unicidad contra BD.

---

## Uso Básico

```php
use Boctulus\Simplerest\Libs\Validator;

$validator = new Validator();

$validator->validate([
    'name'     => 'required|string|max:100',
    'email'    => 'required|email|unique:users',
    'password' => 'required|min:8',
    'age'      => 'integer|min:18|max:120',
], $_POST);

if ($validator->fails()) {
    $errors = $validator->getErrors();
    // manejar errores
}
```

## Reglas de Validación

| Regla | Descripción |
|-------|-------------|
| `required` | Campo obligatorio |
| `string` | Debe ser string |
| `integer` | Debe ser entero |
| `numeric` | Debe ser numérico |
| `boolean` | Debe ser booleano |
| `email` | Debe ser email válido |
| `url` | Debe ser URL válida |
| `ip` | Debe ser IP válida |
| `json` | Debe ser JSON válido |
| `array` | Debe ser array |
| `min:N` | Valor mínimo (numérico) o longitud mínima (string) |
| `max:N` | Valor máximo o longitud máxima |
| `between:N,M` | Entre N y M |
| `in:a,b,c` | Debe estar en la lista |
| `not_in:a,b,c` | No debe estar en la lista |
| `unique:table,field` | Debe ser único en BD |
| `exists:table,field` | Debe existir en BD |
| `confirmed` | Debe tener campo `_confirmation` |
| `date` | Debe ser fecha válida |
| `regex:/patrón/` | Debe coincidir con regex |
| `nullable` | Puede ser nulo |

## Personalización

### Ignorar Campos

```php
$validator->ignoreFields(['csrf_token', '_method']);
```

### Deshabilitar Required (para UPDATEs)

```php
$validator->setRequired(false);
```

### Uniques Personalizados

```php
$validator->setUniques(['email', 'username'], 'users');
```

## Validación con Schema

Las reglas pueden definirse directamente en el schema:

```php
// app/Schemas/main/users.php
'email' => [
    'type'       => 'string',
    'length'     => 255,
    'validation' => ['required', 'email', 'unique:users'],
],
```

## Validación en Modelos

```php
$model = new User(true);
$errors = $model->validate([
    'name'  => 'Juan',
    'email' => 'juan@example.com',
]);
```

## Mensajes de Error (i18n)

Los mensajes se traducen via `Translate::bind('validator')`. Archivos de traducción en `app/Locale/`.

```php
$errors = $validator->getErrors();
// Array asociativo campo → mensaje
```

## Ver También

- [`Schemas.md`](./Schemas.md) — reglas de validación en schemas
- [`i18n.md`](./i18n.md) — internacionalización de mensajes

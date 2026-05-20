---
name: validation-rules
description: Complete reference for the SimpleRest Validator class, all validation rules, schema-based validation, and i18n error messages.
---

# Validation Rules Skill

**File**: `src/framework/Libs/Validator.php`  
**Interface**: `IValidator`

## Basic Usage

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
    // $errors = ['email' => 'The email has already been taken.']
}
```

## Complete Rule Reference

| Rule | Example | Description |
|------|---------|-------------|
| `required` | `'required'` | Field must be present and non-empty |
| `string` | `'string'` | Must be a string |
| `integer` | `'integer'` | Must be an integer |
| `numeric` | `'numeric'` | Must be numeric |
| `boolean` | `'boolean'` | Must be boolean (true/false/1/0) |
| `email` | `'email'` | Must be valid email format |
| `url` | `'url'` | Must be valid URL |
| `ip` | `'ip'` | Must be valid IP address |
| `json` | `'json'` | Must be valid JSON string |
| `array` | `'array'` | Must be an array |
| `min:N` | `'min:18'` | Min numeric value OR min string length |
| `max:N` | `'max:100'` | Max numeric value OR max string length |
| `between:N,M` | `'between:18,120'` | Between N and M |
| `in:a,b,c` | `'in:admin,user,guest'` | Must be one of the listed values |
| `not_in:a,b,c` | `'not_in:deleted,banned'` | Must NOT be one of the listed values |
| `unique:table,field` | `'unique:users,email'` | Must be unique in database table |
| `exists:table,field` | `'exists:categories,id'` | Value must exist in DB table |
| `confirmed` | `'confirmed'` | Requires matching `{field}_confirmation` |
| `date` | `'date'` | Must be valid date |
| `regex:/patrón/` | `'regex:/^[a-z]+$/'` | Must match regex pattern |
| `nullable` | `'nullable'` | Field can be null/empty |

## Schema-Based Validation

Rules can be embedded in schema files:

```php
// app/Schemas/main/users.php
'email' => [
    'type'       => 'string',
    'length'     => 255,
    'validation' => ['required', 'email', 'unique:users'],
],
'age' => [
    'type'       => 'integer',
    'validation' => ['required', 'integer', 'min:18'],
],
```

## Validation in Models

```php
$model = new User(true);
$errors = $model->validate([
    'name'  => 'Juan',
    'email' => 'juan@example.com',
]);

if (!empty($errors)) {
    // handle errors
}
```

## Advanced Configuration

### Ignore Fields

```php
$validator->ignoreFields(['csrf_token', '_method', 'api_key']);
```

### Disable Required (for UPDATEs)

```php
$validator->setRequired(false);
// Now all 'required' rules are skipped — useful for partial updates
```

### Custom Uniques

```php
$validator->setUniques(['email', 'username'], 'users');
// Override which fields/tables to check for uniqueness at runtime
```

## Error Messages (i18n)

Messages are translated via `Translate::bind('validator')`.

Translation files in `app/Locale/`:

```php
// app/Locale/es/validator.php
return [
    'required' => 'El campo :field es obligatorio.',
    'email'    => 'El campo :field debe ser un email válido.',
    'min'      => 'El campo :field debe tener al menos :param caracteres.',
    // ...
];
```

Get errors:

```php
$errors = $validator->getErrors();
// Array asociativo: ['field_name' => 'Mensaje de error traducido']
```

## Common Pitfalls

1. **`unique` on UPDATE** — use `setRequired(false)` AND handle the current record ID to avoid "unique" false positives
2. **`confirmed`** — requires a `{field}_confirmation` in the input data
3. **`regex` delimiters** — must include delimiters like `/pattern/`
4. **`in`/`not_in`** — values are comma-separated, no spaces after commas unless they're part of the value
5. **`min`/`max`** — behavior depends on field type: numeric for `integer`/`numeric`, length for `string`
6. **`nullable`** — must be combined with other rules; alone it does nothing

## Best Practices

1. **Validate in controllers** before any model operation
2. **Use schema validation** for model-level consistency (DRY)
3. **Set `setRequired(false)`** for PATCH/PUT partial updates
4. **Always translate messages** via `app/Locale/` files
5. **Combine `nullable` with type rules** — `'nullable|string|max:255'`
6. **Use `exists`** to verify FK references before inserts

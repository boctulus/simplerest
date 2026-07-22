---
name: validation-rules
description: Complete reference for the SimpleRest Validator class, all validation rules, schema-based validation, and i18n error messages.
---

# Validation Rules Skill

**File**: `src/framework/Libs/Validator.php`

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
}
```

## All Rules

| Rule | Example | Description |
|------|---------|-------------|
| `required` | `'required'` | Field must be present and non-empty |
| `string` | `'string'` | Must be string |
| `integer` | `'integer'` | Must be integer |
| `numeric` | `'numeric'` | Must be numeric |
| `boolean` | `'boolean'` | Must be boolean |
| `email` | `'email'` | Valid email |
| `url` | `'url'` | Valid URL |
| `ip` | `'ip'` | Valid IP |
| `json` | `'json'` | Valid JSON string |
| `array` | `'array'` | Must be array |
| `min:N` | `'min:18'` | Min value or min string length |
| `max:N` | `'max:100'` | Max value or max string length |
| `between:N,M` | `'between:18,120'` | Between N and M |
| `in:a,b,c` | `'in:admin,user,guest'` | Must be in list |
| `not_in:a,b,c` | `'not_in:deleted,banned'` | Must NOT be in list |
| `unique:table,field` | `'unique:users,email'` | Unique in DB |
| `exists:table,field` | `'exists:categories,id'` | Must exist in DB |
| `confirmed` | `'confirmed'` | Requires `{field}_confirmation` |
| `date` | `'date'` | Valid date |
| `regex:/patrón/` | `'regex:/^[a-z]+$/'` | Regex match |
| `nullable` | `'nullable'` | Can be null/empty |

## Schema-Based Validation

```php
// app/Schemas/main/users.php
'email' => [
    'type'       => 'string',
    'validation' => ['required', 'email', 'unique:users'],
],
```

## Model Validation

```php
$model = new User(true);
$errors = $model->validate(['name' => 'Juan', 'email' => 'juan@example.com']);
```

## Advanced

```php
$validator->ignoreFields(['csrf_token', '_method']);
$validator->setRequired(false);                          // for UPDATEs
$validator->setUniques(['email'], 'users');              // custom unique check
```

## i18n Messages

```php
// app/Locale/es/validator.php
return ['required' => 'El campo :field es obligatorio.'];
```

## Pitfalls

1. `unique` on UPDATE — use `setRequired(false)` and handle current record ID
2. `confirmed` — requires `{field}_confirmation` in input data
3. `regex` — must include delimiters like `/pattern/`
4. `min`/`max` — numeric for `integer`, length for `string`
5. `nullable` alone does nothing — combine with other rules

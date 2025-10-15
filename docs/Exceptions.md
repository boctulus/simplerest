# Sistema de Excepciones en SimpleRest

Este documento describe la implementación del sistema de excepciones personalizado del framework **SimpleRest**.  
El objetivo es ofrecer una arquitectura robusta, extensible y traducible para manejar errores en aplicaciones HTTP y CLI.

---

## 📁 Estructura de Archivos

```
app/
└── Core/
    ├── Exceptions/
    │   ├── BaseException.php
    │   └── SomeException.php
    └── Libs/
        └── SystemMessages.php
config/
└── messages.php
```

---

## 🧱 `BaseException.php`

Ruta: `app/Core/Exceptions/BaseException.php`

### Descripción
Clase base abstracta que extiende `\Exception` y sirve como plantilla para todas las excepciones del framework.

### Características
- Compatible con la firma de `\Exception`.
- Permite definir un **código simbólico** (`$errorCode`) en lugar de mensajes duros.
- Carga mensajes desde `SystemMessages`.
- Soporta interpolación de argumentos (`vsprintf`).
- Permite definir un código HTTP, metadatos y mensaje traducido.

### Principales Propiedades
| Propiedad | Tipo | Descripción |
|------------|------|-------------|
| `$errorCode` | `string` | Código simbólico que identifica el tipo de error. |
| `$args` | `array` | Argumentos para interpolar en el mensaje. |
| `$httpStatus` | `int` | Código HTTP asociado. |
| `$meta` | `array` | Datos adicionales para depuración o contexto. |

### Principales Métodos
| Método | Descripción |
|---------|--------------|
| `__construct()` | Constructor compatible con `\Exception`. Resuelve el mensaje automáticamente. |
| `fromErrorCode()` | Factory estático que crea una excepción a partir de un código simbólico. |
| `getErrorCode()` | Retorna el código de error. |
| `getHttpStatus()` | Retorna el código HTTP asociado. |
| `getTranslatedMessage()` | Retorna el mensaje traducido según `SystemMessages`. |
| `toArray()` | Exporta la excepción en formato de arreglo (ideal para respuestas JSON). |

---

## 🧩 `MiddlewareNotFoundException.php`

Ruta: `app/Core/Exceptions/MiddlewareNotFoundException.php`

### Descripción
Excepción específica lanzada cuando no se encuentra un middleware requerido.

### Código
```php
<?php

namespace Boctulus\Simplerest\Core\Exceptions;

use Boctulus\Simplerest\Core\Exceptions\BaseException;

class MiddlewareNotFoundException extends BaseException
{
    protected static string $errorCode = 'HTTP>MIDDLEWARE_NOT_FOUND';
}
```

---

## 🧠 `SystemMessages.php`

Ruta: `app/Core/Libs/SystemMessages.php`

### Descripción
Clase encargada de cargar y resolver los mensajes de error definidos en `config/messages.php`.

### Funciones
- `get($code, ...$args)` — Devuelve el mensaje traducido e interpolado.
- `load()` — Carga los mensajes desde el archivo de configuración.
- Soporta traducción mediante `gettext()` (si está disponible).

### Ejemplo
```php
SystemMessages::get('HTTP>MIDDLEWARE_NOT_FOUND');
// Devuelve: "Middleware not found"
```

---

## ⚙️ `messages.php`

Ruta: `config/messages.php`

### Descripción
Archivo que contiene los mensajes precompilados indexados por código simbólico.

### Ejemplo
```php
return [
    'HTTP>MIDDLEWARE_NOT_FOUND' => 'Middleware not found',
    'HTTP>BAD_REQUEST' => 'Bad request',
    'FILES>FILE_NOT_FOUND' => 'File not found',
];
```

---

## 💡 Ejemplo de Uso

```php
if (!$middlewareFound) {
    throw MiddlewareNotFoundException::fromErrorCode(
        'HTTP>MIDDLEWARE_NOT_FOUND',
        [$middlewareName],
        404,
        0,
        null,
        ['middleware' => $middlewareName]
    );
}
```

Respuesta API esperada:
```json
{
    "type": "HTTP",
    "code": "HTTP>MIDDLEWARE_NOT_FOUND",
    "message": "Middleware not found",
    "http_status": 404,
    "meta": {
        "middleware": "AuthMiddleware"
    }
}
```

---

## ✅ Ventajas del Sistema
- Código desacoplado de los mensajes.
- Traducciones centralizadas y reutilizables.
- Compatibilidad con excepciones estándar de PHP.
- Ideal para respuestas estructuradas en APIs REST.
- Fallback seguro en caso de error de traducción.

---

## 📚 Autoría
**Proyecto:** SimpleRest  
**Componente:** Sistema de Excepciones  
**Versión:** 1.0  
**Lenguaje:** PHP 8.2+

---

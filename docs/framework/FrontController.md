# FrontController / Handler Pipeline

## Arquitectura

El **FrontController** implementa el patrón **Front Controller**: todas las peticiones HTTP pasan por `index.php` que delega en `FrontController::resolve()`.

**Archivo**: `src/framework/FrontController.php`

## Pipeline de 6 Handlers

El FrontController ejecuta handlers en secuencia, cada uno con una responsabilidad específica:

```
index.php
  → WebRouter::compile() / resolve()
  → CliRouter::compile() / resolve()
  → FrontController::resolve()
       → RequestHandler     (1) Parsear request
       → ApiHandler         (2) Resolver rutas /api/*
       → AuthHandler        (3) Resolver rutas /auth
       → OutputHandler      (4) Formatear respuesta
       → MiddlewareHandler   (5) Ejecutar middlewares
       → ErrorHandler       (6) Manejar errores
```

| Handler | Responsabilidad |
|---------|----------------|
| `RequestHandler` | Parsea URL y parámetros del request |
| `ApiHandler` | Captura rutas `/api/*` para endpoints REST automáticos |
| `AuthHandler` | Captura rutas `/auth` para autenticación |
| `OutputHandler` | Formatea la respuesta (JSON, etc.) |
| `MiddlewareHandler` | Ejecuta middlewares post-controlador |
| `ErrorHandler` | Manejo centralizado de excepciones |

## Configuración

Los handlers se configuran en `config/config.php`:

```php
'behaviors' => [
    'handlers' => [
        RequestHandler::class,
        ApiHandler::class,
        AuthHandler::class,
        OutputHandler::class,
        MiddlewareHandler::class,
        ErrorHandler::class,
    ]
]
```

## Handler Personalizado

Se puede crear un handler propio implementando `IProcessable`:

```php
use Boctulus\Simplerest\Interfaces\IProcessable;

class MiHandler implements IProcessable
{
    public function process($data = null)
    {
        // Lógica personalizada
        return $data;
    }
}
```

## Ver También

- [`Routing.md`](./Routing.md) — WebRouter + CliRouter
- [`Framework-Architecture.md`](./Framework-Architecture.md) — arquitectura general

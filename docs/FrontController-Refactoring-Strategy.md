# Estrategia A – Simplificación del FrontController

## Objetivo

Reducir la complejidad del archivo `FrontController.php` delegando comportamientos repetitivos y condicionales a clases separadas llamadas **Handlers** o **Behaviors**, sin eliminar ninguna funcionalidad existente.

El nuevo `FrontController` debe ser **mínimo**, **configurable** y **agnóstico del entorno** (HTTP o CLI).

---

## 1. Principios del Rediseño

### FrontController como orquestador
Su única función debe ser **orquestar** el flujo de ejecución:

- Detectar entorno (CLI o HTTP)
- Delegar el enrutamiento y la resolución de controlador a una clase `Handler`
- Ejecutar middlewares y formatear la respuesta final

### Separación por responsabilidad
Cada bloque condicional actual se moverá a una clase con **responsabilidad única**:

- **RequestHandler** → manejo de rutas HTTP y CLI
- **ApiHandler** → validación de rutas `/api/`
- **AuthHandler** → autenticación y rutas `/auth`
- **OutputHandler** → gestión de formatos de salida
- **MiddlewareHandler** → ejecución de middlewares definidos
- **ErrorHandler** → errores y excepciones

### Configurabilidad total
Todas las clases "handler" deben ser instanciables dinámicamente desde `config/config.php`:

```php
'front_behaviors' => [
    'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
    'api'        => Boctulus\Simplerest\Core\Handlers\ApiHandler::class,
    'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
    'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
    'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
    'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
],
```

Así se puede **reemplazar fácilmente** cualquier comportamiento por una implementación personalizada.

---

## 2. Nueva Estructura Propuesta

```
app/Core/
├── FrontController.php
└── Handlers/
    ├── RequestHandler.php
    ├── ApiHandler.php
    ├── AuthHandler.php
    ├── OutputHandler.php
    ├── MiddlewareHandler.php
    └── ErrorHandler.php
```

---

## 3. Flujo Simplificado

### 3.1. FrontController (mínimo)

**Responsabilidad:** coordinar los handlers según el contexto.

```php
<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Response;

class FrontController
{
    const DEFAULT_ACTION = 'index';

    static function resolve()
    {
        $config = Config::get();
        $res    = Response::getInstance();

        try {
            // 1. Instanciar handlers desde configuración
            $handlers = [];
            foreach ($config['front_behaviors'] as $key => $class) {
                $handlers[$key] = new $class();
            }

            // 2. Determinar tipo de entorno
            $env = php_sapi_name() === 'cli' ? 'cli' : 'http';

            // 3. Obtener parámetros de la request
            [$params, $is_auth, $is_api] = $handlers['request']->parse($env);

            // 4. Resolver clase, método y parámetros
            if ($is_auth) {
                [$class, $method, $args] = $handlers['auth']->resolve($params);
            } elseif ($is_api) {
                [$class, $method, $args] = $handlers['api']->resolve($params);
            } else {
                [$class, $method, $args] = $handlers['request']->resolveController($params);
            }

            // 5. Ejecutar el método del controlador
            $controller = new $class();
            $data = call_user_func_array([$controller, $method], $args);

            // 6. Procesar salida
            $output = $handlers['output']->format($controller, $data);

            // 7. Ejecutar middlewares y enviar respuesta
            $handlers['middleware']->run($class, $method);
            $res->set($output)->flush();

        } catch (\Throwable $e) {
            $handlers['error']->handle($e);
        }
    }
}
```

---

## 4. Descripción de Handlers

### 4.1. RequestHandler
**Responsabilidades:**
- Detecta entorno (HTTP/CLI)
- Extrae y normaliza parámetros (`$_SERVER['REQUEST_URI']` o `$argv`)
- Determina si la solicitud apunta a `/auth` o `/api`

**Métodos sugeridos:**
```php
public function parse(string $env): array;
public function resolveController(array $params): array;
```

**Retorna:**
- `parse()`: `[$params, $is_auth, $is_api]`
- `resolveController()`: `[$className, $methodName, $arguments]`

---

### 4.2. AuthHandler
**Responsabilidades:**
- Encargado exclusivamente de rutas `/auth`
- Valida formato y versión de API si aplica

**Método:**
```php
public function resolve(array $params): array;
```

**Retorna:** `[$className, $methodName, $arguments]`

---

### 4.3. ApiHandler
**Responsabilidades:**
- Procesa rutas `/api`
- Verifica versión de API y método HTTP
- Determina clase, método y argumentos

**Método:**
```php
public function resolve(array $params): array;
```

**Retorna:** `[$className, $methodName, $arguments]`

---

### 4.4. OutputHandler
**Responsabilidades:**
- Determina formato de salida según tipo de controlador:
  - `ApiController` → JSON
  - `ConsoleController` → Texto plano
  - Postman o navegador → Pretty JSON / HTML
- Separa la lógica de conversión (`json_encode`, `Strings::formatOutput`, etc.)

**Método:**
```php
public function format($controller, $data): string;
```

**Retorna:** String formateado según el contexto

---

### 4.5. MiddlewareHandler
**Responsabilidades:**
- Carga `middlewares.php`
- Ejecuta middlewares correspondientes al controlador y método actuales

**Método:**
```php
public function run(string $class, string $method): void;
```

---

### 4.6. ErrorHandler
**Responsabilidades:**
- Centraliza el manejo de errores
- Registra logs o devuelve JSON con error estructurado

**Método:**
```php
public function handle(\Throwable $e): void;
```

---

## 5. Beneficios del Enfoque

✅ **Código modular y limpio**
Cada comportamiento es intercambiable y probado por separado.

✅ **Escalabilidad**
Agregar o modificar flujos (por ejemplo, nuevos tipos de rutas) sin tocar el núcleo.

✅ **Testabilidad**
Los handlers son pequeñas clases testables individualmente.

✅ **Configurabilidad**
`config/config.php` permite elegir implementaciones personalizadas por proyecto o entorno.

✅ **Compatibilidad**
Funciona igual para CLI y HTTP sin perder ninguna característica.

---

## 6. Próximos pasos

1. Crear los archivos `Handlers/*.php` según las firmas descritas.
2. Actualizar `config/config.php` con el nuevo bloque `front_behaviors`.
3. Limpiar el código original de `FrontController` dejando solo el flujo descrito.
4. Documentar en `docs/Routing.md` la nueva arquitectura de front controller minimalista.

---

## 7. Ejemplo de Configuración Final (config/config.php)

```php
'front_behaviors' => [
    'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
    'api'        => Boctulus\Simplerest\Core\Handlers\ApiHandler::class,
    'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
    'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
    'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
    'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
],
```

---

## 8. Resultado Esperado

Un `FrontController` reducido de **más de 400 líneas a menos de 60**,
con el mismo comportamiento, **mejor mantenibilidad** y configuración flexible para futuras extensiones.

---

## 9. Ejemplo de Implementación de un Handler

### RequestHandler.php

```php
<?php

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Libs\Config;

class RequestHandler
{
    /**
     * Parsea la request y determina el entorno
     *
     * @param string $env 'cli' o 'http'
     * @return array [$params, $is_auth, $is_api]
     */
    public function parse(string $env): array
    {
        if ($env === 'cli') {
            global $argv;
            $params = array_slice($argv, 1);
        } else {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = trim($path, '/');
            $params = array_filter(explode('/', $path), 'strlen');
        }

        $is_auth = isset($params[0]) && $params[0] === 'auth';
        $is_api  = isset($params[0]) && $params[0] === 'api';

        return [$params, $is_auth, $is_api];
    }

    /**
     * Resuelve controlador, método y argumentos para rutas normales
     *
     * @param array $params
     * @return array [$className, $methodName, $arguments]
     */
    public function resolveController(array $params): array
    {
        $config = Config::get();
        $namespace = $config['namespace'] . '\\Controllers\\';

        $controllerName = $params[0] ?? $config['default_controller'];
        $className = $namespace . ucfirst($controllerName) . 'Controller';

        if (!class_exists($className)) {
            throw new \Exception("Controller $className not found");
        }

        // Determinar método
        $method = $params[1] ?? 'index';
        $args = array_slice($params, 2);

        if (!method_exists($className, $method)) {
            $method = 'index';
            $args = array_slice($params, 1);
        }

        return [$className, $method, $args];
    }
}
```

---

## 10. Diagrama de Flujo

```
┌─────────────────────┐
│  FrontController    │
│    ::resolve()      │
└──────────┬──────────┘
           │
           ├─► 1. Instanciar Handlers (desde config)
           │
           ├─► 2. RequestHandler::parse($env)
           │      └─► Detecta CLI/HTTP
           │      └─► Extrae parámetros
           │      └─► Detecta /auth o /api
           │
           ├─► 3. Resolver ruta:
           │      ├─► Si /auth  → AuthHandler::resolve()
           │      ├─► Si /api   → ApiHandler::resolve()
           │      └─► Sino      → RequestHandler::resolveController()
           │
           ├─► 4. Ejecutar Controlador
           │      └─► call_user_func_array([$controller, $method], $args)
           │
           ├─► 5. OutputHandler::format()
           │      └─► JSON, HTML, CLI según contexto
           │
           ├─► 6. MiddlewareHandler::run()
           │      └─► Ejecuta middlewares post-controller
           │
           └─► 7. Response::flush()
                  └─► Envía respuesta al cliente

     Errores → ErrorHandler::handle()
```

---

## 11. Ventajas vs Traits

| Aspecto | Handlers (Clases) | Traits |
|---------|------------------|--------|
| **Instanciabilidad** | ✅ Sí, con estado propio | ❌ No, solo código reutilizable |
| **Configurabilidad** | ✅ Desde config.php | ❌ Fijo en código |
| **Reemplazabilidad** | ✅ Total | ⚠️ Parcial (override) |
| **Testabilidad** | ✅ Alta (unit tests aislados) | ⚠️ Media (acoplado a clase) |
| **Inyección de dependencias** | ✅ Sí | ❌ No |
| **Múltiples implementaciones** | ✅ Sí (polimorfismo) | ❌ No |

---

## 12. Plan de Migración

### Fase 1: Crear estructura base
- [ ] Crear directorio `app/Core/Handlers/`
- [ ] Crear interfaces base para cada Handler
- [ ] Implementar `RequestHandler` como prueba de concepto

### Fase 2: Migrar comportamientos uno por uno
- [ ] `RequestHandler` (parsing y resolución básica)
- [ ] `ApiHandler` (lógica de `/api/*`)
- [ ] `AuthHandler` (lógica de `/auth/*`)
- [ ] `OutputHandler` (formateo de respuestas)
- [ ] `MiddlewareHandler` (ejecución de middlewares)
- [ ] `ErrorHandler` (manejo de errores)

### Fase 3: Actualizar configuración
- [ ] Agregar bloque `front_behaviors` en `config/config.php`
- [ ] Documentar configuración personalizada

### Fase 4: Simplificar FrontController
- [ ] Reescribir `FrontController::resolve()` con el nuevo flujo
- [ ] Eliminar código duplicado y condicionales complejos
- [ ] Mantener compatibilidad con API existente

### Fase 5: Testing y documentación
- [ ] Tests unitarios para cada Handler
- [ ] Tests de integración end-to-end
- [ ] Actualizar `docs/Routing.md`
- [ ] Agregar ejemplos de handlers personalizados

---

## 13. Ejemplo de Handler Personalizado

Un usuario puede crear su propio handler y configurarlo:

### CustomApiHandler.php
```php
<?php

namespace MyApp\Handlers;

use Boctulus\Simplerest\Core\Handlers\ApiHandler;

class CustomApiHandler extends ApiHandler
{
    public function resolve(array $params): array
    {
        // Lógica personalizada para API
        // Por ejemplo: agregar versionado diferente

        if ($params[1] === 'v2') {
            // Lógica específica para v2
            return $this->resolveV2($params);
        }

        // Delegar al comportamiento original
        return parent::resolve($params);
    }

    private function resolveV2(array $params): array
    {
        // Implementación custom
        // ...
    }
}
```

### config/config.php
```php
'front_behaviors' => [
    'request'    => Boctulus\Simplerest\Core\Handlers\RequestHandler::class,
    'api'        => MyApp\Handlers\CustomApiHandler::class, // ← Personalizado
    'auth'       => Boctulus\Simplerest\Core\Handlers\AuthHandler::class,
    'output'     => Boctulus\Simplerest\Core\Handlers\OutputHandler::class,
    'middleware' => Boctulus\Simplerest\Core\Handlers\MiddlewareHandler::class,
    'error'      => Boctulus\Simplerest\Core\Handlers\ErrorHandler::class,
],
```

---

## 14. Conclusión

Esta estrategia permite:

1. **Código limpio y mantenible:** FrontController pasa de 400+ líneas a ~60
2. **Flexibilidad total:** Cualquier comportamiento es reemplazable
3. **Testabilidad:** Cada handler es testeable de forma aislada
4. **Escalabilidad:** Agregar nuevos tipos de rutas sin tocar el core
5. **Configurabilidad:** Todo se define en `config/config.php`

El framework mantiene toda su funcionalidad actual mientras gana en modularidad y extensibilidad.

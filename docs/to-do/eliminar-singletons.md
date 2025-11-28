# Eliminando Singletons en SimpleRest: Diseño, Explicación y Beneficios

Este documento explica de manera clara e incremental **cómo sería SimpleRest si NO utilizara Singletons para Request y Response**, y qué impacto tendría en la arquitectura, en los controladores, en el router y en los tests unitarios.  

Está orientado a desarrolladores que ya dominan APIs REST, pero que desean entender cómo funciona una arquitectura más moderna basada en *dependency injection*.

---

# 1. El problema con los Singletons

Hoy SimpleRest hace esto:

- `Request::getInstance()` devuelve la única instancia global de la petición.
- `Response::getInstance()` devuelve la única instancia global para devolver la salida.

Esto acopla los controladores al estado global del framework e introduce problemas como:

- Dificultad para testear (hay que reemplazar estados globales).
- Posibles efectos colaterales entre pruebas.
- Imposibilidad de tener múltiples Request/Response en un proceso persistente.
- Dependencias ocultas que los controladores no declaran.

---

# 2. ¿Qué pasaría si NO se usaran Singletons?

El framework utilizaría **inyección de dependencias (DI)**.  
Los controladores recibirían explícitamente sus dependencias en el constructor:

- Una instancia de `Request`
- Una instancia de `Response`

Esto se conoce como **constructor injection**, y es el método preferido en arquitecturas limpias.

---

# 3. Cómo se vería un controlador sin Singletons

## ✔ Versión actual (con Singletons)

```php
class UsersController {
    public function create() {
        $req = Request::getInstance();
        $body = $req->getBody(true);

        Response::getInstance()->json(['ok' => true]);
    }
}
```

---

## ✔ Nueva versión (sin Singletons)

```php
class UsersController 
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function create()
    {
        $body = $this->request->getBody(true);
        $this->response->json(['ok' => true]);
    }
}
```

Beneficios:

- El controlador no conoce el mecanismo interno de cómo se instancian dependencias.
- El flujo queda declarado de forma explícita.
- Facilita pruebas, mantenimiento y legibilidad.

---

# 4. ¿Quién construye Request/Response si no hay Singletons?

El router o dispatcher se encargaría de crear estas instancias:

```php
$request  = new Request($_SERVER, $_GET, $_POST, file_get_contents('php://input'));
$response = new Response();

$controller = new UsersController($request, $response);
$controller->create();
```

Esto es lo que hacen frameworks profesionales como:

- Laravel (mediante un contenedor DI)
- Symfony
- Slim
- FastAPI
- Express.js

---

# 5. Cómo mejora el testing unitario

## ✔ Hoy (con Singletons)

- Hay que usar `setInstance()` para reemplazar Request/Response.
- Luego hay que limpiar los Singletons en `tearDown()`.
- Si un test falla, otros pueden verse afectados.
- Hay estado global que debe ser reseteado.

## ✔ Sin Singletons → Mucho más simple

```php
public function testCreateUser()
{
    $mockRequest = $this->createMock(Request::class);
    $mockRequest->method('getBody')
        ->with(true)
        ->willReturn(['name' => 'Pablo']);

    $mockResponse = $this->createMock(Response::class);
    $mockResponse->expects($this->once())
        ->method('json')
        ->with(['ok' => true]);

    $controller = new UsersController($mockRequest, $mockResponse);

    $controller->create();
}
```

Ya no hace falta:

- `setInstance()`
- `tearDown()`
- manipular estado global
- resetear nada

Se prueban objetos en **aislamiento real**.

---

# 6. Beneficios arquitectónicos de eliminar Singletons

### ✔ Diseño más limpio y moderno
Los controladores declaran sus dependencias de forma explícita.

### ✔ Desacoplamiento total
El controlador no depende del ciclo de vida del framework.

### ✔ Tests más simples y robustos
El mocking es directo y no requiere manipular estado global.

### ✔ Compatibilidad con servidores persistentes (Swoole, ReactPHP)
Los Singletons NO funcionan bien en servidores que mantienen estado entre peticiones.

### ✔ Más flexible
Puedes reemplazar Request/Response por versiones extendidas, decoradores, middlewares, etc.

### ✔ Permite usar contenedores DI profesionales
Como Symfony DI, PHP-DI o frameworks híbridos.

---

# 7. Consideraciones y cambios necesarios

### 1. El router debe encargarse de instanciar dependencias
Pequeño costo de implementación, pero mejora enorme en arquitectura.

### 2. Controladores deben aceptar objetos Request/Response
El cambio es sencillo pero muy significativo.

### 3. Deben eliminarse llamadas estáticas como:
- `Request::getInstance()`
- `Response::getInstance()`

### 4. Middleware y helpers deben adaptarse
En lugar de acceder a Singletons, deben recibir dependencias explícitas.

---

# 8. Conclusión

Eliminar los Singletons en SimpleRest tendría un impacto muy positivo:

- Controladores más limpios, declarativos y mantenibles.
- Tests unitarios mucho más sencillos, sin dolores de cabeza.
- Framework modernizado y alineado con buenas prácticas.
- Extensibilidad real del pipeline HTTP.
- Compatibilidad con servidores modernos y arquitecturas concurrentes.

Adoptar DI en lugar de Singletons haría que SimpleRest sea un framework más robusto, flexible y profesional.

# Unit Tests en SimpleRest: Introducción y Clarificación de Conceptos

Este documento explica de forma **incremental, clara y práctica** cómo realizar *unit tests* dentro del framework **SimpleRest**, con énfasis en el uso correcto de **mockRequest** y **mockResponse** cuando el framework utiliza **Singletons**.

Su objetivo es guiarte paso a paso desde los conceptos básicos hasta el uso aplicado en PHPUnit.

---

# 1. ¿Qué es un Unit Test?

Un **unit test** es una prueba automatizada que verifica el comportamiento de una unidad específica del sistema: una función, un método o un componente aislado.  
Su propósito es:

- Detectar errores temprano  
- Asegurar comportamientos consistentes  
- Permitir refactorizar sin temor  
- Evitar dependencias externas (como HTTP real, base de datos real, etc.)

---

# 2. ¿Por qué necesitamos mocks?

En un test unitario queremos aislar la lógica.  
Pero un controlador depende de:

- `Request` → datos enviados por el cliente  
- `Response` → mecanismo para devolver salida  

Ambos son clases globales y en SimpleRest funcionan como **Singletons**.  
Si no los sustituimos, los controladores:

- Leerán el Request real creado por PHP  
- Escribirán al Response real (salida al navegador)

Eso imposibilita las pruebas.

Por eso se crean **mocks**, que son objetos falsos controlados por PHPUnit que reemplazan temporalmente a los Singletons reales.

---

# 3. ¿Qué es un mock en PHPUnit?

Un **mock** es un objeto que:

1. Se comporta como la clase original  
2. Puedes configurar para que devuelva valores específicos  
3. Te permite verificar que un método interno fue llamado o no  
4. Te permite controlar parámetros y resultados de forma precisa  

Ejemplo: simular que `getBody()` devuelve un JSON específico.

---

# 4. ¿Por qué usar setInstance()?

SimpleRest gestiona Request y Response mediante el patrón Singleton.  
Para reemplazar temporalmente la instancia global se usa:

```php
Request::setInstance($mockRequest);
Response::setInstance($mockResponse);
```

Esto garantiza que **cuando el controlador pida las instancias**, recibirá tus mocks configurados.

---

# 5. Bootstrap para correr unit tests

El siguiente bootstrap es necesario para cargar el framework, sus constantes, helpers y autoloaders:

```php
<?php

use PHPUnit\Framework\TestCase;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../../../app.php';

/*
    Ejecutar con: `./vendor/bin/phpunit {ruta de este archivo}`
*/
```

Este archivo es obligatorio para que PHPUnit tenga acceso a:

- Constantes globales  
- Helpers  
- Configuración del framework  
- Autoload de clases  

---

# 6. Mocking de Request y Response

## 6.1 Crear mocks correctamente

### ❌ INCORRECTO:
```php
$mockRequest->method('getBody')->with(true)->willReturn($data);
```

### ✅ CORRECTO:
```php
$mockRequest->expects($this->any())
    ->method('getBody')
    ->with(true)
    ->willReturn($data);
```

`expects()` permite verificar que un método fue:

- llamado  
- llamado N veces  
- llamado con parámetros específicos  

---

## 6.2 Inyectar mocks en Singletons

### ❌ INCORRECTO:
```php
$GLOBALS['mockRequest'] = $mockRequest;
```

### ✅ CORRECTO:
```php
Request::setInstance($mockRequest);
Response::setInstance($mockResponse);
```

Esto garantiza que los controladores usan los mocks durante la prueba.

---

## 6.3 Limpiar Singletons al terminar la prueba

Obligatorio para evitar contaminación entre tests:

```php
protected function tearDown(): void
{
    Request::setInstance(null);
    Response::setInstance(null);

    parent::tearDown();
}
```

---

# 7. Ejemplo completo de test unitario

```php
public function testEmitDTEWithSdkException()
{
    $requestBody = [
        'dteData' => [
            'Encabezado' => [
                'IdDoc' => ['TipoDTE' => 33],
            ],
        ]
    ];

    // Mock de Request
    $mockRequest = $this->createMock(Request::class);
    $mockRequest->expects($this->any())
        ->method('getBody')
        ->with(true)
        ->willReturn($requestBody);

    // Mock de Response
    $mockResponse = $this->createMock(Response::class);
    $mockResponse->expects($this->once())
        ->method('status')
        ->with(500);

    $mockResponse->expects($this->once())
        ->method('json')
        ->with($this->callback(function($data) {
            return is_array($data) &&
                   isset($data['success']) &&
                   $data['success'] === false;
        }));

    // Inyección de mocks en los Singletons
    Request::setInstance($mockRequest);
    Response::setInstance($mockResponse);

    // Crear el controlador
    $controller = new OpenFacturaController();

    // Ejecutar método privado o protegido con Reflection
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('emitDTE');
    $method->invoke($controller);
}
```

---

# 8. Notas importantes

1. Siempre sustituir Request y Response usando `setInstance()`.  
2. Nunca usar `$GLOBALS` para mocks.  
3. Configurar mocks con `expects()->method()->with()->willReturn()`.  
4. Limpiar Singletons en `tearDown()`.  
5. Los tests deben **invocar realmente** el método del controlador.  

---

# 9. Conclusión

El uso correcto de mocks en conjuntos con el patrón Singleton del framework es esencial para garantizar que las pruebas unitarias sean:

- Confiables  
- Aisladas  
- Repetibles  
- No dependientes del entorno ni del flujo real de HTTP  

Con estas prácticas, puedes testear cualquier controlador del framework SimpleRest de forma segura y precisa.

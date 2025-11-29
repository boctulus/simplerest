# Unit Tests en SimpleRest: Introducción y Clarificación de Conceptos

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

---

# 3. ¿Qué es un mock en PHPUnit?

Un **mock** es un objeto que:

1. Se comporta como la clase original  
2. Puedes configurar para que devuelva valores específicos  
3. Te permite verificar que un método interno fue llamado o no  
4. Te permite controlar parámetros y resultados de forma precisa  

Ejemplo: simular que `getBody()` devuelve un JSON específico.

---

# 4. Bootstrap para correr unit tests

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

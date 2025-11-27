# UNIT TESTS

Para poder tener acceso a todo el framework incluidas constantes (definidas en constants.php) asi como helpers y demas es fundamental el siguiente boostrapping en el archivo del script de las pruebas unitarias: 

```
<?php

use PHPUnit\Framework\TestCase;
// otros "use"(s)

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../../../app.php';

/*
	Ejecutar con: `./vendor/bin/phpunit {ruta de este archivo}` desde el root del proyecto
*/

//
// Resto del archivo con las pruebas unitarias
//
```

Si son muchas pruebas otra opcion es crear un archivo bootstrap.php dentro de la carpeta:

```
<?php

/**
 * Bootsraping a incluir en pruebas unitarias para tener acceso completo al framework y packages
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../../../../app.php';
```

y luego incluirlo:

```
<?php

use PHPUnit\Framework\TestCase;
// otros "use"(s)

require_once __DIR__ . '/bootstrap.php';

/**
 * Prueba unitaria
 *
 * Ejecutar con: `./vendor/bin/phpunit {ruta de este archivo}` desde el root del proyecto
 */

// Resto de la prueba

```
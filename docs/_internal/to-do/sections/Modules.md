# Modules en SimpleREST

### Creación de un Module

Los modules se crean con el comando `make module`:

```bash
php com make module <nombre-module> [opciones]
```

**Opciones disponibles:**
- `-f, --force`: Sobrescribir si ya existe
- `-r, -d, --remove, --delete`: Eliminar el module

**Ejemplo:**

```bash
php com make module tax-calculator
```

Esto creará la estructura completa en `App/Modules/tax-calculator/`.

### Estructura Generada

```
App/Modules/tax-calculator/
├── assets/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── third_party/
├── config/
│   └── config.php          # ← Archivo de configuración por defecto
├── database/
│   ├── migrations/
│   └── seeders/
├── etc/
├── logs/
├── src/
│   ├── Controllers/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Libs/
│   ├── Middlewares/
│   ├── Models/
│   └── Traits/
├── tests/
├── views/
└── tax-calculator.php      # ← Archivo principal del módulo
```

### Uso de Plantillas

Si existe una carpeta de plantillas en `app/templates/class_templates/Module/`, el comando copiará y parseará automáticamente esos archivos en lugar de crear archivos por defecto.

Esto permite tener templates pre-configurados para diferentes tipos de módulos.

### Eliminación de un Module

```bash
php com make module tax-calculator --remove
# o
php com make module tax-calculator -d
```

### Forzar Sobrescritura

```bash
php com make module tax-calculator --force
# o
php com make module tax-calculator -f
```

### Integración con el Framework

Los modules están más integrados con el framework que los packages:

1. **Ubicación:** Dentro de `App/Modules/`, participan del namespace principal
2. **Autoloading:** Pueden usar el autoloader principal del framework
3. **Configuración:** Acceso directo a la configuración del framework
4. **Recursos:** Pueden acceder directamente a helpers, traits y librerías del framework

### Uso Típico en WordPress

Los modules son especialmente útiles cuando se porta SimpleREST a WordPress:

```php
// En un plugin de WordPress
require_once MODULES_PATH . 'tax-calculator/tax-calculator.php';

// El módulo puede tener toda su lógica encapsulada
TaxCalculator::init();
```

### Acceso a Constantes

Los modules tienen acceso directo a constantes del framework:

```php
// Dentro de un module
require_once MODULES_PATH . 'otro-modulo/config/config.php';
Files::download($url, MODULES_PATH . "tax-calculator/assets/js");
```

### Routing necesario

Digamos que tenemos un modulo "typeform" que muestra un formulario y que es procesado con un metodo process() dentro de la clase Typeform.

Para que, en este caso, el modulo "typeform" funcione fue necesario en routes.php (rutas del WebRouter) configurar rutas para APIs y servir vistas:

// Ejemplo

WebRouter::get("typeform", function() use ($route) {
	set_template('templates/tpl_bt3.php');          
	render(Typeform::get());
});

WebRouter::post("typeform/process", function() use ($route) {
	render(Typeform::process());
});

En general si el objetivo del modulo es servir vistas o presentar una API entonces es necesario configurar algun tipo de router.

A diferencia de los "packages", la configuracion de rutas debe hacerse a nivel de la carpeta "config" del framework.

\config\routes.php
\config\cli_routes.php


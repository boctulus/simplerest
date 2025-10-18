# Commandos personalizados

El framework implementa comandos personalizados bajo el patron "Command" y se generan con "php com make command {nombre}"

Ej:

	php com make command myCommand

Cada comando tiene un metodo handle($args) que se puede redefinir (generalmente no es necesario) y un metodo help() requerido por la interfaz ICommand.

## CommandTrait

Todos los comandos usan el trait `CommandTrait` que provee funcionalidad común:

### parseOptions()

Parsea opciones de línea de comandos en formato `--key=value`, `--key:value`, o `--key` (flags booleanos).

**Características:**

- Soporta formato `--key=value` y `--key:value`
- Convierte guiones a guiones bajos (`--dry-run` → `dry_run`)
- Elimina comillas de los valores (`--name="John"` → `name: "John"`)
- Flags booleanos: `--verbose` → `verbose: true`

**Ejemplo:**

```php
public function myMethod(...$options)
{
    $opts = $this->parseOptions($options);
    $limit = $opts['limit'] ?? 100;
    $dryRun = $opts['dry_run'] ?? false;
    $name = $opts['name'] ?? null;
}
```

### getOption()

Helper para obtener valores parseados con valor por defecto.

**Ejemplo:**

```php
public function myMethod(...$options)
{
    $opts = $this->parseOptions($options);
    $limit = $this->getOption($opts, 'limit', 100);
    $verbose = $this->getOption($opts, 'verbose', false);
}
```

### Comandos con opciones personalizadas

Si necesitas valores por defecto o parseo específico, puedes sobrescribir `parseOptions()`:

```php
protected function parseOptions(array $args): array {
    // Valores por defecto específicos
    $defaults = [
        'pattern' => '*.*',
        'recursive' => false,
    ];

    // Normalizar aliases
    $normalizedArgs = [];
    foreach ($args as $arg) {
        if ($arg === '-r') {
            $normalizedArgs[] = '--recursive';
        } else {
            $normalizedArgs[] = $arg;
        }
    }

    // Parsear (inline o llamar a método auxiliar)
    $options = [];
    foreach ($normalizedArgs as $arg) {
        if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $matches)) {
            $key = str_replace('-', '_', $matches[1]);
            $value = trim($matches[2], '"\'');
            $options[$key] = $value;
        } elseif (preg_match('/^--(.+)$/', $arg, $matches)) {
            $key = str_replace('-', '_', $matches[1]);
            $options[$key] = true;
        }
    }

    return array_merge($defaults, $options);
}
```

### Pruebas unitarias

El framework incluye pruebas unitarias para `CommandTrait` en `tests/CommandTraitTest.php`:

```bash
# Ejecutar pruebas del CommandTrait
./vendor/bin/phpunit tests/CommandTraitTest.php
```

Las pruebas cubren:
- Parseo con formato `--key=value` y `--key:value`
- Flags booleanos (`--dry-run`)
- Valores con comillas (`--name="John Doe"`)
- Conversión de guiones a guiones bajos
- Casos reales de ZippyCommand, FileCommand y ModuleCommand
- Helper getOption() con valores por defecto

## Ejemplo completo de comando

	<?php

	use Boctulus\Simplerest\Core\Libs\DB;
	use Boctulus\Simplerest\interfaces\ICommand;

	class MysqlLogCommand implements ICommand 
	{
		/*
			Redefino handler()
		*/
		function handle($args){
			$fst = array_shift($args);

			if ($fst == 'on'){
				dd("Iniciando logs ...");
				DB::dbLogOn();
				return;
			}

			if ($fst == 'off'){
				dd("Desactivando logs ...");
				DB::dbLogOff();
				return;
			}

			if ($fst == 'start'){
				dd("Activando logs ...");
				DB::dbLogStart();
				return;
			}

			if ($fst == 'dump'){
				dd("Volcando logs ...");
				DB::dbLogDump();
				return;
			}         
		}   
		
		/*
			Proveo ayuda requerida
		*/
		function help($name = null, ...$args){
			$str = <<<STR
			php com mysql_log on                                  DB::dbLogOn()
			php com mysql_log off                                 DB::dbLogOff()
			php com mysql_log start [-filename=]  que ejecuta ..  DB::dbLogStart()       
			php com mysql_log dump                                DB::dbLogDump() 
			STR;

			dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
			dd($str);
		}
	} 


El trait MakeCommand viene con varios metodos utiles para crear nuevos comandos:

# renderTemplate

## description

Método utilitario para crear archivos a partir de plantillas con opciones de personalización.

## usage

```php
$this->renderTemplate($name, $prefix, $subfix, $dest_path, $template_path, $namespace, ...$opt);
```

## parameters

- - `$name`: Nombre base del archivo (ej. `my_controller`).
- - `$prefix`: Prefijo opcional para la clase (ej. `I` para interfaces).
- - `$subfix`: Sufijo para la clase (ej. `Controller`).
- - `$dest_path`: Ruta base donde se guardará el archivo.
- - `$template_path`: Ruta de la plantilla a utilizar.
- - `$namespace`: Namespace opcional para la clase.
- - `...$opt`: Opciones como `--force`, `--unignore`, `--strict`, `--remove`.

## examples

- 1. **Crear un controlador:**
   ```php
   $this->renderTemplate('my_controller', '', 'Controller', CONTROLLERS_PATH, self::TEMPLATES . 'Controller.php', $this->namespace . '\\controllers', '--force');
   ```
   - Genera `MyControllerController.php` en `CONTROLLERS_PATH`.
- 2. **Crear una interfaz:**
   ```php
   $this->renderTemplate('my_interface', 'I', '', INTERFACE_PATH, self::INTERFACE_TEMPLATE, $this->namespace . '\\interfaces', '--strict');
   ```
   - Genera `IMyInterface.php` con `declare(strict_types=1);`.
- 3. **Eliminar un archivo:**
   ```php
   $this->renderTemplate('my_controller', '', 'Controller', CONTROLLERS_PATH, self::TEMPLATES . 'Controller.php', $this->namespace . '\\controllers', '--remove');
   ```
   - Elimina `MyControllerController.php` si existe.

# makeScaffolding

## description

Crea una estructura de directorios para scaffoldings y soporta opciones como `--force` y `--remove`.

## usage

```php
$this->makeScaffolding($directories, $basePath, $options);
```

## parameters

- - `$directories`: Array de subdirectorios a crear (ej. `['assets/css', 'config']`).
- - `$basePath`: Directorio base donde se crearán los subdirectorios.
- - `$options`: Opciones como `--force`, `--remove`.

## examples

- 1. **Crear estructura de módulo:**
   ```php
   $this->makeScaffolding(['assets/css', 'config'], MODULES_PATH . 'myModule', ['--force']);
   ```
   - Crea `assets/css` y `config` en `myModule`, sobrescribiendo si es necesario.
- 2. **Eliminar estructura:**
   ```php
   $this->makeScaffolding(['assets/css', 'config'], MODULES_PATH . 'myModule', ['--remove']);
   ```
   - Elimina los subdirectorios especificados si existen.

# Comando SQL

El comando `sql` permite ejecutar operaciones SQL desde la línea de comandos.

## Subcomandos disponibles

### sql list

Lista el contenido de una tabla específica con soporte para paginación y formato de tabla.

#### Sintaxis

```bash
php com sql list '{db}.{table}' [--offset=N] [--limit=N] [--format=table]
```

#### Parámetros

- `{db}.{table}`: Nombre de la conexión de base de datos y tabla en formato `conexion.tabla`
- `--offset=N`: Número de registros a saltar (opcional, por defecto: 0)
- `--limit=N`: Número máximo de registros a mostrar (opcional, por defecto: 10)
- `--format=table`: Formato de salida. Valores posibles:
  - Sin especificar: formato simple (un registro por bloque)
  - `table`: formato de tabla ASCII con bordes

#### Ejemplos

```bash
# Listar los primeros 10 registros de la tabla users
php com sql list 'main.users'

# Listar los primeros 20 registros
php com sql list 'main.users' --limit=20

# Listar 5 registros empezando desde el registro 10
php com sql list 'main.users' --offset=10 --limit=5

# Mostrar resultados en formato de tabla ASCII
php com sql list 'main.users' --format=table

# Listar 50 productos de otra conexión en formato tabla
php com sql list 'db_195.products' --limit=50 --format=table
```

#### Notas

- La conexión de base de datos especificada debe estar registrada en `db_connections`
- El formato de tabla ASCII es útil para visualizar datos tabulares de manera más clara
- Si no se especifica `--limit`, por defecto se muestran 10 registros
- El formato simple muestra cada registro en un bloque separado con sus campos

# Zippy Command

Comandos para la gestión de productos y categorías de Zippy.

## test_mapping

Prueba el mapeo de una categoría raw sin guardar en la base de datos.

### Sintaxis

```bash
php com zippy test_mapping --raw="<value>" [--strategy=<strategy>]
```

### Parámetros

- `--raw="<value>"`: (Requerido) El texto de la categoría a probar.
- `--strategy=<strategy>`: (Opcional) La estrategia a utilizar. Valores posibles: `llm`, `fuzzy`. Por defecto es `llm`.

### Ejemplo

```bash
# Probar mapeo usando la estrategia por defecto (llm)
php com zippy test_mapping --raw="Aceites Y Condimentos"

# Probar mapeo forzando la estrategia fuzzy
php com zippy test_mapping --raw="Aceites Y Condimentos" --strategy=fuzzy
```

## products_process_categories

Procesa los productos de la base de datos para asignarles las categorías correspondientes basándose en sus datos.

### Sintaxis

```bash
php com zippy products_process_categories [--limit=<N>] [--dry-run]
```

### Parámetros

- `--limit=<N>`: (Opcional) Limita el número de productos a procesar.
- `--dry-run`: (Opcional) Ejecuta el comando en modo de simulación sin guardar los cambios en la base de datos.

### Ejemplo

```bash
# Procesar 100 productos en modo de simulación
php com zippy products_process_categories --limit=100 --dry-run

# Procesar 500 productos y guardar los cambios
php com zippy products_process_categories --limit=500
```

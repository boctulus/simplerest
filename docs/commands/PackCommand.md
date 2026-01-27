# Comando PackCommand

Se ha creado un comando `php com pack` cuyo objetivo es hacer una copia limpia de SimpleRest framework a otro directorio donde sera testeado, sufrira limpiezas adicionales en cuarentena antes de ser distribuido.

## Responsabilidades

Entre otras tareas el comando `php com pack` tiene como responsabilidades:

- Copiar los archivos relevantes asegurandose ignorar al copiar cualquier archivo definido en .cpignore

- Crear el scaffolding completo de directorios en destino

- Procesar archivos de configuración (composer.json, .env.example, config/middlewares.php)

- Debe hacer un "composer install" en destino

- Al ejecutarse 'php com pack' se debe asegurar que en destino (`..\simplerest-pack\`) funcione:

	* El comando `php ..\simplerest-pack\com help` (eso implica copiar tambien "com")

	* El script `php ..\simplerest-pack\runalltests.php`  (no deberia mostrar errores y deberia leerse "All tests passed!" )

	* Al hacer un curl a "http://simplerest.test/" no deberia leerse "Error" o "Exception"

El propio comando deberia hacer las verificaciones pertinentes en destino.

## Scaffolding Creado en Destino

El comando crea la siguiente estructura de directorios en destino:

```
config/
database/
database/migrations/
database/seeders/
logs/
public/
public/assets/
public/assets/css/
public/assets/img/
public/assets/js/
public/assets/fonts/
scripts/
scripts/init/
scripts/init/boot/
scripts/init/redirection/
storage/
third_party/
etc/
app/
app/Modules/
app/Controllers/
app/Controllers/Api/
app/Background/
app/Background/Cronjobs/
app/Background/Tasks/
app/Commands/
app/DAO/
app/DTO/
app/Exceptions/
app/Helpers/
app/Interfaces/
app/Libs/
app/Middlewares/
app/Models/
app/Transformers/
app/Schemas/
app/Traits/
app/Views/
app/Locale/
app/Widgets/
vendor/
```

## Proceso de Verificación

Durante la verificación, el comando:

1. **Crea temporalmente archivos necesarios para tests**:
   - `tests/bootstrap.php` - Bootstrap de PHPUnit que inicializa la conexión DB
   - Modelos necesarios: `MyModel.php`, `ProductsModel.php`, `UsersModel.php`
   - Schemas necesarios: `ProductsSchema.php`, `UsersSchema.php`

2. **Modifica temporalmente `composer.json`** para incluir:
   ```json
   "Boctulus\\Simplerest\\Controllers\\": "app/Controllers/",
   "Boctulus\\Simplerest\\Models\\": "app/Models/",
   "Boctulus\\Simplerest\\Schemas\\": "app/Schemas/"
   ```

3. **Ejecuta las verificaciones**:
   - `php com help`
   - `php runalltests.php`
   - Health check via curl

4. **Limpia todo**:
   - Restaura `composer.json` original
   - Elimina modelos y schemas temporales
   - Elimina `tests/bootstrap.php`
   - Restaura `phpunit.xml`
   - Regenera autoload con configuración limpia

5. **Sanitiza `.env.example`** después de las pruebas

## Archivos de Configuración Procesados

### config/middlewares.php

El archivo `config/middlewares.php` en destino se crea con el siguiente contenido limpio:

```php
<?php

/*
    Middleware registration
*/

return [
    /*
         Examples
    */
    // 'Boctulus\Simplerest\Controllers\TestController' => InjectGreeting::class
];
```

### composer.json

El archivo `composer.json` se procesa para:
- Remover repositorios de tipo "path" (desarrollo local)
- Remover sección autoload-dev
- Limpiar require a solo dependencias mínimas (php, vlucas/phpdotenv)
- Mantener solo dependencias dev esenciales (phpunit, phpstan)
- Remover sección scripts

### .env.example

El archivo `.env.example` se sanitiza para:
- Vaciar credenciales de base de datos
- Vaciar credenciales de email
- Vaciar tokens secretos
- Vaciar credenciales OAuth
- Vaciar credenciales Redis

## Opciones del Comando

```bash
php com pack [opciones]
```

Opciones disponibles:
- `-s, --source PATH`: Directorio origen (por defecto: D:\laragon\www\simplerest)
- `-d, --dest PATH`: Directorio destino (por defecto: D:\laragon\www\simplerest-pack)
- `-V, --skip-verification`: Omitir verificación automatizada en destino

## Ejemplos de Uso

```bash
# Empaquetar con valores por defecto
php com pack

# Omitir verificación
php com pack --skip-verification

# Especificar directorios personalizados
php com pack -s /custom/source -d /custom/dest
```
---
title: Configuración y entorno
source:
  - docs/core/configuration-environment.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Configuración y entorno

`Config` mantiene configuración en memoria. Los helpers de entorno cargan archivos y valores del proceso por rutas distintas, así que no tienen la misma precedencia.

## Configuración en memoria

`Config::get()` carga `config/config.php` y `config/databases.php`, combina los arrays y guarda el resultado en memoria estática durante el proceso. `Config::get('base_url', $default)` acepta rutas con puntos; `Config::set()` escribe una ruta y `Config::add()` añade un elemento al array de esa ruta. Para combinar en la raíz, la firma `string $property` permite `''`, `'/'` o `'.'`; no acepta `null` aunque el cuerpo revise ese valor.

`loadPackageConfig($vendor, $package, $array)` guarda valores en `packages.{vendor}.{package}`. `getPackageConfig()` busca primero allí y luego usa la clave global. `config($key)` delega en `Config::get()` y `set_cfg($key, $value)` delega en `Config::set()`.

`get_cfg($key)` incluye el archivo desde `CONFIG_PATH` cuando `$key` termina en `.php`; para otras claves consulta la configuración en memoria. Por ejemplo, el helper CORS usa `get_cfg('cors.php')`.

## Carga de entorno

Si `app.php` encuentra la clase opcional `Dotenv\Dotenv`, carga `.env` mediante `createImmutable(__DIR__)`. La clase propia `Env` usa otro camino: `Env::get()` y `Env::getBool()` cargan sus datos de forma diferida, y `config/config.php` llama esos métodos al construirse.

`Env::setup()` elige `.dev-env` cuando existe y no está vacío; si no, usa `.env`. Si `.env` falta, busca `env.example` y lo copia como `.env`. La plantilla del checkout se llama `.env.example`, por lo que ese fallback no coincide con su nombre. Una llamada inicial a `Env::get()` puede escribir un archivo cuando falta `.env`.

El helper global `env($key, $default)` revisa primero `getenv($key)`. Si el proceso tiene el valor, incluso si es una cadena vacía, lo devuelve; en caso contrario consulta `Env::get()`. Una llamada directa a `Env::get()` no comprueba el entorno del proceso.

`Env::parse()` reconoce comentarios completos con `#` o `;`, el prefijo opcional `export `, BOM, finales de línea comunes, claves sin `=`, valores vacíos y valores entre comillas. En valores sin comillas, `#` o `;` inician comentario cuando tienen un espacio delante. Las comillas dobles reconocen `\"` y `\\`; otras secuencias con barra se conservan. Las cabeceras de sección se ignoran.

## Verificación y límites

`EnvParserTest.php` y `EnvHelperPrecedenceTest.php` contienen aserciones sobre parsing y precedencia del entorno de proceso; se inspeccionaron, no se ejecutaron. No se reprodujo el bootstrap ni la carga de archivos de entorno. La firma y el nombre fallback descritos arriba son límites del código actual.

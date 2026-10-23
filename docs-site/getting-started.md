---
title: Primeros pasos
source:
  - docs/getting-started/README.md
  - docs/architecture.md
source_revision: f6d74e69c055f9f73b697b825a3595ddee592e01
status: partial
---

# Primeros pasos

## Antes de instalar

::: tip Flujo básico verificado en Windows (2026-10-22)
Un clon nuevo de `docu` en `f6d74e69` instaló Composer, generó `.env` con el helper y respondió HTTP 200 en `/system/health` y en un endpoint propio `/hello`. La comprobación no usó una base de datos. El flujo de consumidor Composer y el CRUD con base de datos siguen pendientes.
:::

## Datos comprobados del paquete

- El paquete Composer se identifica como `boctulus/simplerest`, de tipo `library`.
- El requisito declarado es PHP `>=8.1,<8.5`.
- Composer asigna las clases `Boctulus\Simplerest\Core\` a `src/framework/`.
- `index.php` carga el `app.php` de la raíz; ese bootstrap prepara autoloading, entorno, configuración y proveedores de la aplicación.
- `composer create-project` desde el release `v1.0.3` se verificó hasta la respuesta HTTP `{"ok":true}` de `/system/health`. Este resultado corresponde a ese release.
- Un clon limpio de `docu` en Windows, revisión `f6d74e69`, completó `composer install`; el helper creó `.env` y `/system/health` respondió HTTP 200.

## Estado de las rutas

| Ruta | Lo que se comprobó | Lo que falta |
| --- | --- | --- |
| Usar el checkout actual como aplicación | Un clon Windows de `docu` en `f6d74e69` instaló 40 paquetes, creó `.env` con el helper y devolvió HTTP 200 desde `/system/health` y un endpoint `/hello` añadido para la comprobación. | El smoke usó DB y correo dummy en `127.0.0.1:1`; no acredita una conexión de base de datos ni CRUD. |
| Crear una aplicación con Composer | El release `v1.0.3` instaló dependencias, creó `.env` y devolvió `{"ok":true}` desde `/system/health`. | Este resultado del release no acredita el flujo de consumidor Composer ni una instalación desde el checkout fuente. |
| Consumirlo como dependencia Composer | El metadato Composer declara un paquete de biblioteca. | Una prueba de consumidor local no resolvió `boctulus/shopifyconnector @dev`; no se comprobó una instalación desde el registro público ni el bootstrap del consumidor. |

## Crear una aplicación desde el release publicado

En Windows con PHP 8.3.36 y Composer 2.8.5, estos comandos completaron el flujo de `v1.0.3`:

```powershell
composer create-project boctulus/simplerest mi-simplerest 1.0.3 --no-interaction --prefer-dist --no-progress
Set-Location .\mi-simplerest
php -S 127.0.0.1:18732 index.php
```

En otra terminal, ejecuta `curl.exe -i http://127.0.0.1:18732/system/health`. La respuesta reproducida fue HTTP 200, `Content-type:application/json;charset=utf-8` y `{"ok":true}`. El evento `post-root-package-install` creó `.env` desde `.env.example` y vació valores sensibles. Composer instaló 40 paquetes y emitió advertencias del autoloader optimizado por clases ambiguas y archivos que no cumplen PSR-4.

## Añadir un primer endpoint

Añade esta ruta junto a las demás en `config/routes.php`:

```php
WebRouter::get('hello', function () {
    return ['message' => 'Hello from SimpleRest'];
});
```

Desde la raíz del proyecto, inicia PHP con `index.php` como router y ejecuta las peticiones desde otra terminal:

```powershell
php -S 127.0.0.1:18789 index.php
```

```powershell
curl.exe -i http://127.0.0.1:18789/system/health
curl.exe -i http://127.0.0.1:18789/hello
```

En el smoke de Windows, ambas rutas respondieron HTTP 200: `/system/health` devolvió `{"ok":true}` y `/hello` devolvió `{"message":"Hello from SimpleRest"}`. La ruta personalizada no usa base de datos.

## Reproducción histórica del checkout (revisión `1c1444526`)

En un archivo limpio de los archivos rastreados, `composer install` instaló dependencias pero no creó `.env`. La primera petición a `/system/health` falló en `Dotenv::load()` porque no encontró ese archivo; el servidor PHP devolvió HTTP 200 con el fatal en el cuerpo. Al ejecutar explícitamente `composer run-script post-root-package-install`, se creó `.env`; en aquella revisión la compilación de rutas encontró referencias activas a `DumbController`, ausente del checkout. En `0c7ef58`, esas referencias están comentadas. La reproducción posterior en `f6d74e69` cerró el smoke HTTP básico para el checkout actual; el resultado de `v1.0.3` sigue correspondiendo a su release.

::: details Comandos probados en el checkout actual
```powershell
composer install --no-interaction --prefer-dist --no-progress
composer run-script post-root-package-install
php -S 127.0.0.1:18789 index.php
curl.exe -i http://127.0.0.1:18789/system/health
curl.exe -i http://127.0.0.1:18789/hello
```
El servidor debe ejecutarse en otra terminal para enviar la petición.
:::

## Entorno y arranque

`Env::setup()` busca `env.example`, pero el archivo rastreado se llama `.env.example`. Además, `app.php` llama a `Dotenv::load()` antes de llegar a `Env::setup()`, por lo que esa ruta alternativa no resuelve la ausencia de `.env`. El evento `post-root-package-install` genera el archivo; el fallo de compilación descrito arriba sólo se confirmó en `1c1444526`, no en el código actual.

El repositorio también contiene los puntos de entrada de una aplicación configurada. Su presencia no demuestra que el paquete pueda consumirse sin la estructura y configuración de esa aplicación.

## Qué no se debe inferir

Las respuestas verificadas no acreditan una conexión a base de datos ni el uso como dependencia Composer. El clon actual completó el arranque HTTP mínimo, pero el CRUD con base de datos y el flujo de consumidor siguen sin verificarse; por eso esta página permanece parcial.

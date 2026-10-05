---
title: Primeros pasos
source:
  - docs/getting-started/README.md
  - docs/architecture.md
source_revision: 51aacd1a21301d8d162ecf7dd6fd2ee0e3397742
status: partial
---

# Primeros pasos

## Antes de instalar

::: warning El resultado depende de la ruta de instalación
Un proyecto creado desde el release de Packagist `v1.0.3` respondió correctamente en `/system/health`. Un archivo limpio del checkout actual instala dependencias, pero su bootstrap HTTP queda bloqueado; el uso como dependencia Composer tampoco se ha verificado de principio a fin.
:::

## Datos comprobados del paquete

- El paquete Composer se identifica como `boctulus/simplerest`, de tipo `library`.
- El requisito declarado es PHP `>=8.1,<8.5`.
- Composer asigna las clases `Boctulus\Simplerest\Core\` a `src/framework/`.
- `index.php` carga el `app.php` de la raíz; ese bootstrap prepara autoloading, entorno, configuración y proveedores de la aplicación.
- `composer create-project` desde el release `v1.0.3` se verificó hasta la respuesta HTTP `{"ok":true}` de `/system/health`. Este resultado corresponde a ese release.

## Estado de las rutas

| Ruta | Lo que se comprobó | Lo que falta |
| --- | --- | --- |
| Usar este checkout como aplicación | `composer install --no-interaction --prefer-dist --no-progress` terminó en un archivo limpio de fuentes rastreadas (PHP 8.3.15, Composer 2.8.5). | El bootstrap falla sin `.env`; con `.env` generado, la compilación de rutas falla porque falta `DumbController`. No se obtuvo una respuesta funcional. |
| Crear una aplicación con Composer | El release `v1.0.3` instaló dependencias, creó `.env` y devolvió `{"ok":true}` desde `/system/health`. | Este resultado no acredita que el checkout actual tenga el mismo contenido ni que se pueda consumir como biblioteca. |
| Consumirlo como dependencia Composer | El metadato Composer declara un paquete de biblioteca. | Una prueba de consumidor local no resolvió `boctulus/shopifyconnector @dev`; no se comprobó una instalación desde el registro público ni el bootstrap del consumidor. |

## Crear una aplicación desde el release publicado

En Windows con PHP 8.3.15 y Composer 2.8.5, estos comandos completaron el flujo de `v1.0.3`:

```powershell
composer create-project boctulus/simplerest mi-simplerest 1.0.3 --no-interaction --prefer-dist --no-progress
Set-Location .\mi-simplerest
php -S 127.0.0.1:18732 -t .
```

En otra terminal, ejecuta `curl.exe -i http://127.0.0.1:18732/system/health`. La respuesta reproducida fue HTTP 200, `Content-type:application/json;charset=utf-8` y `{"ok":true}`. El evento `post-root-package-install` creó `.env` desde `.env.example` y vació valores sensibles. Composer instaló 40 paquetes y emitió advertencias del autoloader optimizado por clases ambiguas y archivos que no cumplen PSR-4.

## Checkout limpio del repositorio actual

En un archivo limpio de los archivos rastreados, `composer install` instaló dependencias pero no creó `.env`. La primera petición a `/system/health` falló en `Dotenv::load()` porque no encontró ese archivo; el servidor PHP devolvió HTTP 200 con el fatal en el cuerpo. Al ejecutar explícitamente `composer run-script post-root-package-install`, se creó `.env`, pero la ruta siguió bloqueada: `config/routes.php` registra `DumbController`, ausente del checkout y excluido por `.gitignore`. En esa segunda petición el cuerpo estaba vacío y el servidor registró el error fatal de `WebRouter::compile()`. El éxito del release `v1.0.3` no demuestra que el checkout actual arranque.

::: details Comandos probados en el checkout actual
```powershell
composer install --no-interaction --prefer-dist --no-progress
composer run-script post-root-package-install
php -S 127.0.0.1:18731 -t .
curl.exe -i http://127.0.0.1:18731/system/health
```
El servidor debe ejecutarse en otra terminal para enviar la petición.
:::

## Entorno y arranque

`Env::setup()` busca `env.example`, pero el archivo rastreado se llama `.env.example`. Además, `app.php` llama a `Dotenv::load()` antes de llegar a `Env::setup()`, por lo que esa ruta alternativa no resuelve la ausencia de `.env`. El evento `post-root-package-install` genera el archivo, pero eso no corrige el fallo de compilación de rutas.

El repositorio también contiene los puntos de entrada de una aplicación configurada. Su presencia no demuestra que el paquete pueda consumirse sin la estructura y configuración de esa aplicación.

## Qué no se debe inferir

El resultado reproducido de `v1.0.3` no acredita una conexión a base de datos ni el uso como dependencia Composer. El checkout actual tampoco arranca con el procedimiento de Composer descrito. Esta página permanece parcial mientras ese checkout y el flujo de consumidor sigan sin verificarse correctamente.

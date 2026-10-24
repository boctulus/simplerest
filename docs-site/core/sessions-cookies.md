---
title: Sesiones y cookies
source:
  - docs/core/sessions-cookies.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Sesiones y cookies

`Session` y `Cookie` son wrappers estáticos del runtime de PHP. No definen una política global de sesiones ni defaults de seguridad para cookies.

## Métodos de sesión

La mayoría de métodos de datos llama a `Session::start()`, que ejecuta `session_start()` solo si PHP indica `PHP_SESSION_NONE`. `set($key, $value)`, `get($key, $default)`, `has($key)`, `delete($key)` y `clear()` leen o modifican `$_SESSION`; `clear()` llama a `session_unset()`.

`destroy()` inicia la sesión si hace falta y llama a `session_destroy()`. `regenerateId($deleteOld)` delega en `session_regenerate_id()` y `getId()` inicia la sesión antes de devolver `session_id()`. `getName()` y `setName()` usan `session_name()` sin iniciar la sesión.

`flash($key, $value)` guarda en `$_SESSION['_flash']`. Llamarlo sin valor o con `null` lee y elimina la entrada; no se puede guardar `null` como valor flash distinguible. `hasFlash($key)` comprueba si existe.

## Métodos de cookie

`Cookie::set($name, $value, $expiry = 0, $path = '/', $domain = '', $secure = false, $httponly = false)` elimina del `$_COOKIE` de la request actual la entrada si existía y delega en `setcookie()` con esos argumentos. No escribe el nuevo valor en `$_COOKIE`; el navegador lo enviará en otra request.

`get($name = null)` lee todo `$_COOKIE` o una entrada. `delete($name, $path = '/', $domain = '')` elimina la entrada local y envía una cookie expirada. Estas firmas no exponen SameSite y `delete()` no pasa opciones secure ni HttpOnly.

## Verificación y límites

Los métodos se trazaron en `Session.php` y `Cookie.php`. No se ejecutó ninguna operación de sesión/cookie ni se infirió el comportamiento de un navegador.

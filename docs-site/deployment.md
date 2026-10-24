---
title: Despliegue
source:
  - docs/deployment/README.md
source_revision: 7354ea996d206b90398a4cd0e5e5526711182a5e
status: partial
---

# Despliegue

En este checkout, el despliegue automatizado corresponde al sitio de documentación. No hay una receta validada para instalar y operar una aplicación SimpleRest en producción.

## Sitio de documentación

`.github/workflows/public-docs.yml` construye el sitio en pull requests hacia `master` y construye/publica GitHub Pages en pushes a `master`. También permite ejecución manual. El workflow usa Node.js 22, `npm ci` y `npm run build` en `docs-site/`, y publica `.vitepress/dist`.

VitePress usa `base: '/simplerest/'`. Si cambia el dominio o la subruta, hay que revisar esa base y probar los enlaces directos. Un build exitoso no demuestra por sí mismo que GitHub Pages esté publicado y accesible.

## Release del core

El manifiesto Composer cataloga SimpleRest como biblioteca PHP con requisito `>=8.1,<8.5`; generar un paquete core no despliega una aplicación web. Consulta [Microcore y paquetes](/core/microcore) para los límites entre core y extensiones.

## Aplicación SimpleRest

No se encontró un workflow de aplicación ni una configuración Compose actual que describa un servicio completo. Los archivos bajo `docker/` no son una plantilla vigente:

- `docker/php/Dockerfile` parte de PHP-FPM 7.4.6, incompatible con el requisito actual del manifiesto Composer. Además descarga Composer sin verificación TLS y lo actualiza sin fijar versión.
- `docker/nginx/Dockerfile` parte de Nginx 1.19. El archivo de Nginx usa el web root `/appdata/www/public`, puerto 8090 y un upstream FastCGI externo `legionsr-be:9000`; no configura TLS.
- `docker/php/php.ini` no se validó junto con una imagen funcional de la aplicación.

Por eso esta guía no proporciona comandos de despliegue productivo. Para una aplicación concreta aún falta validar el runtime y sus extensiones, el lockfile, web root y rewrites, TLS y proxy, secretos y permisos, conexión/backup de base de datos, migraciones y una solicitud de humo sobre staging.

El ciclo HTTP y la configuración de entrada se explican en [Arquitectura](/architecture) y [Ciclo de request](/core/request-lifecycle). La lista anterior identifica verificaciones pendientes; no acredita que se hayan ejecutado en una aplicación concreta.

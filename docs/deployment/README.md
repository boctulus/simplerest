# Despliegue

Esta referencia separa el despliegue que está automatizado en el repositorio —el sitio de documentación— del despliegue de una aplicación SimpleRest. El checkout actual no contiene una receta validada para instalar y operar una aplicación SimpleRest en producción. Los archivos Docker presentes son antiguos y no deben tomarse como una configuración de producción vigente.

## Sitio de documentación

`.github/workflows/public-docs.yml` es el flujo de despliegue configurado. En pull requests hacia `master` construye el sitio para validar los cambios. En pushes a `master` construye y publica el artefacto en GitHub Pages; también admite ejecución manual sobre `master`.

El workflow fija Node.js 22, instala dependencias con `npm ci`, ejecuta `npm run build` en `docs-site/`, sube `.vitepress/dist` y realiza el deploy de Pages. La configuración VitePress usa `base: '/simplerest/'`; un cambio de dominio o subruta requiere revisar esa base y los enlaces directos. El archivo interno `.github/DOCS-PUBLISHING.md` describe el build local; un build correcto no confirma que GitHub Pages esté publicado y accesible.

## Release del core Composer

El manifiesto raíz cataloga SimpleRest como `library` y declara PHP `>=8.1,<8.5`; no es por sí mismo una imagen ni un servicio web desplegable. La [guía de proceso de release Composer](../architecture/composer-release-process.md) describe la construcción pack-first del core, la validación del snapshot y la separación entre generar un artefacto y autorizar una publicación. Ese ciclo publica una dependencia PHP; no despliega una aplicación productiva.

## Despliegue de una aplicación

No hay un workflow de aplicación ni un archivo Compose actual en el checkout que defina el servicio completo. La configuración bajo `docker/` no ofrece una base validada:

- `docker/php/Dockerfile` parte de PHP-FPM 7.4.6, incompatible con el requisito actual `>=8.1,<8.5` del manifiesto Composer del core. También descarga Composer con verificación TLS desactivada y actualiza Composer sin fijar su versión.
- `docker/nginx/Dockerfile` parte de Nginx 1.19. El `default.conf` escucha en el puerto 8090, define `/appdata/www/public` como web root y reenvía PHP a un servicio FastCGI externo llamado `legionsr-be:9000`. No contiene una configuración TLS.
- `docker/php/php.ini` configura límites del proceso, pero no se validó como configuración activa de subida ni se probó junto a una imagen desplegable.

Por estas diferencias no se incluyen comandos para lanzar ni migrar a producción. Antes de publicar una aplicación hay que validar, sobre el artefacto exacto y un entorno de staging:

1. Versión de PHP, extensiones, lockfile y dependencias requeridas por la aplicación y sus paquetes.
2. Web root y reglas de rewrite frente al entry point y ciclo HTTP descritos en [Arquitectura](../architecture.md) y [ciclo HTTP](../core/request-lifecycle.md).
3. Terminación TLS, headers de proxy, URL base y rutas de archivos con la configuración real del servidor.
4. Variables y secretos, permisos de escritura, conexión de base de datos y estrategia de backup.
5. Migraciones, health checks y una solicitud de humo reproducida en staging antes de promover el mismo artefacto.

La lista identifica evidencia necesaria; no acredita que estos pasos se hayan ejecutado en una aplicación concreta. El [ledger de auditoría](../audit/README.md#deployment-and-benchmark-evidence-2026-10-24) registra el estado de los archivos de despliegue observados.

---
title: Proveedores de servicios
source:
  - docs/core/service-providers.md
source_revision: c35004584563a369ff6f97aea60988d09a6273d8
status: partial
---

# Proveedores de servicios

`app.php` recorre en orden la lista `providers`, crea cada proveedor y llama primero a `register()` y luego a `boot()`. Es un punto de extensión del arranque; no implica que el proveedor registre enlaces en `Container`.

## Contrato del proveedor

`IServiceProvider` declara `register()` y `boot()`. La clase abstracta `ServiceProvider` implementa `boot()` y deja `register()` abstracto, de modo que cada proveedor hijo define su registro.

El `boot()` base usa Reflection para encontrar el directorio del archivo de la clase hija. Si existe `../config/config.php`, carga el array bajo `packages.{vendor}.{package}`; `vendor` y `package` son los dos primeros segmentos del namespace, en minúsculas. Después incluye `../config/routes.php` y `../config/cli_routes.php` junto al directorio de la clase, si existen.

La implementación base no revisa los switches de router HTTP o CLI antes de incluir esos archivos. No descubre cualquier archivo del paquete ni conecta automáticamente dependencias de constructor.

## Verificación y límites

El ciclo se trazó en `app.php`, `IServiceProvider`, `ServiceProvider` y `Config::loadPackageConfig()`. No se ejecutó un proveedor en una aplicación aislada ni se cargaron archivos de rutas de paquetes en esta auditoría.

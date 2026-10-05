---
title: Primeros pasos
source:
  - docs/getting-started/README.md
  - docs/architecture.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

# Primeros pasos

## Antes de instalar

::: warning Aún no hay una instalación de principio a fin verificada
Las dos rutas soportadas —ejecutar este repositorio como aplicación y consumirlo como dependencia Composer— siguen sin reproducirse desde un entorno limpio hasta una petición HTTP. Esta página orienta sobre lo que se conoce; no es un tutorial de copia y ejecución.
:::

## Datos comprobados del paquete

- El paquete Composer se identifica como `boctulus/simplerest`, de tipo `library`.
- El requisito declarado es PHP `>=8.1,<8.5`.
- Composer asigna las clases `Boctulus\Simplerest\Core\` a `src/framework/`.
- `index.php` carga el `app.php` de la raíz; ese bootstrap prepara autoloading, entorno, configuración y proveedores de la aplicación.
- Se verificó `create-project` contra la versión `v1.0.3` de Packagist. Esa comprobación no valida el bootstrap ni una petición.

## Estado de las rutas

| Ruta | Lo que se comprobó | Lo que falta |
| --- | --- | --- |
| Usar este checkout como aplicación | `composer install` terminó en una copia limpia filtrada. | No se ejecutaron el bootstrap de la aplicación ni una petición HTTP. |
| Consumirlo como dependencia Composer | El metadato Composer declara un paquete de biblioteca. | Una prueba de consumidor local no resolvió `boctulus/shopifyconnector @dev`; no se comprobó una instalación desde el registro público ni el bootstrap del consumidor. |

## Entorno y arranque

El manejo del archivo de entorno necesita corrección o una reproducción antes de describirse como paso de instalación: el bootstrap intenta cargar `.env`, mientras que la configuración de `Env::setup()` busca `env.example` sin el punto inicial cuando intenta crear el archivo ausente. No se afirma que el framework cree `.env` automáticamente.

El repositorio también contiene los puntos de entrada de una aplicación configurada. Su presencia no demuestra que el paquete pueda consumirse sin la estructura y configuración de esa aplicación.

## Qué no se debe inferir

La instalación de dependencias, el metadato del paquete y la revisión de `create-project` no acreditan por sí solos un arranque limpio, una conexión a base de datos ni un primer endpoint funcional. Los pasos ejecutables se añadirán cuando la fuente canónica registre una reproducción completa.

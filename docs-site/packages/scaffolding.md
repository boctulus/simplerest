---
title: "Scaffolding y herramientas de desarrollo"
source:
  - docs/packages/scaffolding.md
source_revision: a7043c2dcaee2475031b763a3093e3408ac51b34
---

# Scaffolding y herramientas de desarrollo

`boctulus/simplerest-scaffolding` contiene los comandos `php com make` y sus generadores de archivos para proyectos SimpleRest. Su manifiesto requiere `boctulus/simplerest` y `boctulus/simplerest-i18n ^1.0`; ya no declara `nikic/php-parser`.

`PHPParser`, `CodeReducer` y `PostmanGenerator` se distribuyen en paquetes separados: `boctulus/simplerest-phpparser` (con `nikic/php-parser ^5.4`), `boctulus/simplerest-codereducer` y `boctulus/simplerest-postman-gen`. `Obfuscator` y los cleaners Angular/Bootstrap no aparecen en el código actual de estos paquetes.

## Habilitar los comandos

Instalar el paquete no habilita su grupo. La aplicación anfitriona debe incluirlo en `config/commands.php`:

```php
'packages' => ['boctulus/simplerest-scaffolding'],
```

Después de registrar el entry point `com`, consulta los comandos disponibles:

```bash
php com make help
```

## Generar una API REST para una tabla

Con la conexión `main` configurada y una tabla `products` existente:

```bash
php com make any products --schema --model --api --from=main
```

El generador combinado crea un descriptor de esquema, un modelo y un controlador API que hereda las operaciones CRUD genéricas de `ApiController`. No crea la tabla SQL. También puedes generar cada artefacto por separado:

```bash
php com make schema products --from=main
php com make model products
php com make api products --from=main
```

Revisa los archivos generados, configura las reglas de acceso y consulta los parámetros de [endpoints REST automáticos](/api/automatic-endpoints).

## Otros generadores y límites

El paquete incluye generadores para controladores, comandos, módulos, proveedores y otros artefactos de la aplicación. Revisa la ayuda de cada comando antes de ejecutarlo.

Los generadores pueden escribir o transformar archivos del proyecto anfitrión; revisa el destino y el diff antes de conservar su salida. El paquete no reemplaza `app/Commands/` ni crea el entry point `com`. La opción `--force` puede sobrescribir archivos donde el generador la admite, así que úsala después de revisar el destino. `PHPParser::convertStaticToNonStatic()` pertenece a su paquete separado y sigue siendo experimental, con limitaciones conocidas.

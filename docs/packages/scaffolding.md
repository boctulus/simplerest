# Scaffolding y herramientas de desarrollo

`boctulus/simplerest-scaffolding` contiene el grupo de comandos `make` y sus generadores de archivos para proyectos SimpleRest. Su manifiesto requiere `boctulus/simplerest` y `boctulus/simplerest-i18n ^1.0`; ya no declara `nikic/php-parser`.

Las otras herramientas que antes figuraban junto a scaffolding se distribuyen por separado: `PHPParser` está en `boctulus/simplerest-phpparser` (con `nikic/php-parser ^5.4`), `CodeReducer` en `boctulus/simplerest-codereducer` y `PostmanGenerator` en `boctulus/simplerest-postman-gen`. `Obfuscator` y los cleaners Angular/Bootstrap no aparecen en el código actual de estos paquetes.

## Habilitar comandos

Instalar el paquete no habilita su grupo. La aplicación anfitriona debe incluirlo expresamente en `config/commands.php`:

```php
'packages' => ['boctulus/simplerest-scaffolding'],
```

Después de registrar el entry point `com`, los comandos del grupo pueden aparecer en el [catálogo CLI](../cli/commands.md). Por ejemplo, `php com make model users` genera archivos basados en la configuración y convenciones del proyecto anfitrión.

## Límites

Los generadores pueden escribir o transformar archivos del host; revisa el destino y el diff antes de conservar su salida. El paquete no reemplaza `app/Commands/` ni crea el entry point de la aplicación. `PHPParser::convertStaticToNonStatic()` sigue siendo experimental y tiene limitaciones conocidas, según su paquete separado. No se ejecutó ningún generador durante la documentación.

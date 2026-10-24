# Scaffolding y herramientas de desarrollo

`boctulus/simplerest-scaffolding` contiene el grupo de comandos `make`, `PHPParser`, `CodeReducer`, `Obfuscator`, `PostmanGenerator` y limpiadores Angular/Bootstrap. Requiere SimpleRest, `nikic/php-parser ^5.4` e `i18n ^1.0`.

## Habilitar comandos

Instalar el paquete no habilita su grupo. La aplicación anfitriona debe incluirlo expresamente en `config/commands.php`:

```php
'packages' => ['boctulus/simplerest-scaffolding'],
```

Después de registrar el entry point `com`, los comandos del grupo pueden aparecer en el [catálogo CLI](../cli/commands.md). Por ejemplo, `php com make model users` genera archivos basados en la configuración y convenciones del proyecto anfitrión.

## Límites

Los generadores y cleaners pueden escribir o transformar archivos del host; revisa el destino y el diff antes de conservar su salida. El paquete no reemplaza `app/Commands/` ni crea el entry point de la aplicación. La conversión estática-a-instancia de `PHPParser` es experimental y tiene limitaciones conocidas. No se ejecutó ningún generador durante la documentación.

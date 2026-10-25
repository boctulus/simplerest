---
title: Scaffolding
source:
  - docs/packages/scaffolding.md
source_revision: 7c1de1d260c4b56fc0e7098a6c5f5ac2854e1879
---

# Generadores y scaffolding

El paquete opcional `boctulus/simplerest-scaffolding` incluye los comandos `php com make` para crear componentes de una aplicación SimpleRest a partir de plantillas y metadatos de las tablas.

## Habilitar los comandos

Con el paquete presente en la instalación, habilítalo en `config/commands.php`:

```php
'packages' => ['boctulus/simplerest-scaffolding'],
```

Después, consulta los comandos disponibles:

```bash
php com make help
```

## Generar una API REST para una tabla

Con la conexión `main` configurada y una tabla `products` existente:

```bash
php com make any products --schema --model --api --from=main
```

El generador combinado crea tres artefactos: un descriptor de esquema, un modelo y un controlador API que hereda las operaciones CRUD genéricas de `ApiController`. No crea la tabla SQL.

También puedes generar cada artefacto por separado:

```bash
php com make schema products --from=main
php com make model products
php com make api products --from=main
```

Revisa los archivos generados, configura las reglas de acceso y utiliza los parámetros descritos en [endpoints REST automáticos](/api/automatic-endpoints).

## Otros generadores

El paquete incluye generadores para controladores, comandos, módulos, proveedores y otros artefactos de aplicación. Revisa la ayuda de cada comando antes de ejecutarlo.

La opción `--force` permite sobrescribir archivos donde el generador la admite. Utilízala solamente después de revisar los cambios del destino.

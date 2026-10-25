---
title: Primeros pasos
source:
  - docs/getting-started/README.md
  - docs/architecture.md
source_revision: f6d74e69c055f9f73b697b825a3595ddee592e01
---

# Primeros pasos

SimpleRest requiere PHP `>=8.1,<8.5` y Composer. Este ejemplo utiliza la versión publicada `v1.0.3` como proyecto inicial y después incorpora una ruta HTTP propia.

## Crear una aplicación

```powershell
composer create-project boctulus/simplerest my-simplerest 1.0.3 --no-interaction --prefer-dist
Set-Location .\my-simplerest
php -S 127.0.0.1:18732 index.php
```

Desde otra terminal:

```powershell
curl.exe -i http://127.0.0.1:18732/system/health
```

La respuesta de la ruta de salud es JSON y, con la configuración correcta, contiene `{"ok":true}`.

El proceso de instalación de `create-project` prepara `.env` a partir de `.env.example` mediante el script Composer `post-root-package-install`. Si estás trabajando desde un clon del repositorio de desarrollo y `.env` no existe, ejecuta explícitamente `composer run-script post-root-package-install` después de `composer install`.

## Añadir un endpoint

Registra una ruta en `config/routes.php` junto a las demás definiciones:

```php
WebRouter::get('hello', function () {
    return ['message' => 'Hello from SimpleRest'];
});
```

Inicia el servidor en la raíz del proyecto:

```powershell
php -S 127.0.0.1:18789 index.php
```

Desde otra terminal:

```powershell
curl.exe -i http://127.0.0.1:18789/hello
```

Respuesta:

```json
{"message":"Hello from SimpleRest"}
```

## Crear recursos REST desde una base de datos

Con una conexión configurada, una tabla existente y el paquete de scaffolding habilitado en `config/commands.php`, puedes generar el esquema, modelo y controlador API:

```bash
php com make any products --schema --model --api --from=main
```

Configura las reglas ACL del recurso y consulta la [guía de endpoints automáticos](/api/automatic-endpoints) para usar filtros, selección de campos, relaciones y agregaciones.

## Seguir aprendiendo

- [Resolución HTTP y routing](/core/request-lifecycle)
- [Conexiones y Query Builder](/database)
- [ACL y autorización](/security/acl)
- [Scaffolding y paquetes](/packages/scaffolding)

La instalación de un proyecto nuevo y el consumo como dependencia Composer son flujos distintos. Utiliza las instrucciones del paquete o starter que corresponda a tu caso y versión.

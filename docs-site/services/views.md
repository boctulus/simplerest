---
title: Vistas y layouts PHP
source:
  - docs/services/views.md
source_revision: 88a81da51b6c8c4c035180e3004023391205f616
status: partial
---

# Vistas y layouts PHP

El motor incluye archivos PHP de la aplicación. `VIEWS_PATH` apunta por defecto a `app/Views/`; en este checkout, el layout configurado es `templates/tpl.php`.

## Renderizar

`view()` captura la salida de una vista y la incluye en un layout. Añade `.php` si falta; el layout recibe las variables extraídas y `$content` con el HTML de la vista.

```php
view('users/list', ['users' => $users], 'templates/admin.php');
```

La vista se busca bajo `VIEWS_PATH` y puede pasarse también como ruta absoluta. El layout se resuelve anteponiendo `VIEWS_PATH` al nombre. Si se omite, se usa `config['template']`.

`render($content, $layout, $vars)` incluye un layout sin cargar un archivo de vista; `get_view()` devuelve contenido sin layout y `section()` incluye un subarchivo. Sus firmas y rutas difieren: no son aliases intercambiables.

## Assets y caché

`css_file()`, `js_file()`, `css()` y `js()` agregan entradas a colas estáticas; `head()` y `footer()` las imprimen. Las rutas relativas de los archivos CSS/JS apuntan a `/public/assets/`; las absolutas pasan por `base_url()`. Aunque `js_file()` acepta `$atts`, `get_js()` sólo emite `src` en este checkout.

El `expiration_time` de `view()` cachea el contenido bajo `CACHE_PATH/views/`, identificado por ruta, no por variables, usuario o petición. El layout se incluye en cada render. Otros métodos de `View` tienen rutas y reglas de expiración distintas; `expiration_time = 0` no ofrece un modo uniforme de desactivar todas las variantes.

## Escape y límites

Las vistas son archivos PHP incluidos y el motor no escapa variables, contenido, atributos ni URLs. Codifica los datos según el contexto de salida, por ejemplo con `htmlspecialchars()` para texto HTML. La cola estática de assets no tiene un método público para vaciarse; revisa su ciclo de vida en procesos persistentes.

Se inspeccionaron la clase, helpers y configuración. No se ejecutó un render HTTP ni se midió la caché; no se encontró una suite focalizada de vistas en `unit-tests/`. Consulta [HTML y formularios](/services/forms) para markup y [validación del servidor](/services/validation) para reglas de datos.

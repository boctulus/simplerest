# Vistas y layouts PHP

El motor de vistas de SimpleRest incluye archivos PHP de la aplicación. `VIEWS_PATH` apunta por defecto a `app/Views/`, y la opción `template` selecciona el layout predeterminado; en este checkout vale `templates/tpl.php`.

## Renderizar una vista

`view()` crea una instancia de `View`, captura la salida del archivo indicado y luego incluye el layout. Si la ruta no termina en `.php`, la clase añade esa extensión. El layout recibe las variables extraídas y la variable `$content` con el HTML de la vista.

```php
view('users/list', ['users' => $users], 'templates/admin.php');
```

Si se omite el layout, se usa `config['template']`. `View` busca la vista relativa bajo `VIEWS_PATH`; también acepta una ruta absoluta para el archivo de vista. El layout siempre se forma anteponiendo `VIEWS_PATH` al nombre recibido.

`render($content, $layout, $vars)` es otro camino: incluye el layout con contenido y variables sin cargar un archivo de vista. `get_view()` devuelve contenido sin layout; `section()` incluye otro archivo bajo `VIEWS_PATH`. Sus firmas y rutas difieren, así que no los trates como aliases intercambiables de `view()`.

## Assets en el layout

Las funciones `css_file()`, `js_file()`, `css()` y `js()` agregan entradas a colas estáticas de `View`. El layout puede imprimir `head()` y `footer()` para obtener las etiquetas y el código encolado:

```php
<?= head() ?>
<!-- contenido del layout -->
<?= footer() ?>
```

Las rutas relativas de `css_file()` y `js_file()` se convierten a URLs bajo `/public/assets/`; las rutas absolutas se convierten usando `base_url()`. Pasa rutas absolutas cuando el asset vive en otra carpeta. Aunque `js_file()` acepta `$atts`, la salida de `get_js()` actualmente solo genera `src` y no emite esos atributos.

## Caché del contenido

El parámetro `expiration_time` de `view()` habilita una caché en archivos bajo `CACHE_PATH/views/`. El contenido en caché se identifica por la ruta de la vista, no por las variables, el usuario ni la ruta de petición; evita usarlo para contenido personalizado sin una clave adicional propia. El layout se incluye en cada render y no forma parte del archivo cacheado.

La clase también declara `get_view_src()`, `get_view()` y `destroyCache()`, pero sus rutas de caché y reglas de expiración no son idénticas a las del constructor. No supongas que todas las variantes comparten la misma entrada ni que `expiration_time = 0` crea un modo uniforme de caché desactivada.

## Salida y seguridad

Las vistas son PHP incluido y el contenido se inserta directamente en el layout. El motor no escapa automáticamente variables, contenido, atributos HTML ni URLs. Codifica los valores al contexto de salida —por ejemplo con `htmlspecialchars()` para texto HTML— y evita incluir contenido no confiable como PHP.

La cola de assets también guarda estado estático y no expone un método público para vaciarla. En procesos persistentes, revisa su ciclo de vida para evitar reutilizar entradas de un render anterior.

## Límites de verificación

Se inspeccionaron `View`, los helpers y la configuración de rutas y layout. No se encontró una suite focalizada de `View` bajo `unit-tests/`; no se ejecutó un render HTTP ni se midió la caché. Para generar controles de interfaz de usuario, consulta [HTML y formularios](forms.md); la validación del servidor tiene una [referencia separada](validation.md).

# Routing CLI con CliRouter

`CliRouter` registra comandos invocados en CLI. Esta referencia es independiente del ciclo HTTP y de `WebRouter`; la selección global de resolvers está en [Arquitectura](../architecture.md).

## Archivo y registro

La aplicación registra rutas CLI en `config/cli_routes.php`; `index.php` lo incluye cuando `console_router` está activo. Los ServiceProvider de paquetes también pueden cargar el archivo CLI del paquete. Las rutas de aplicación incluidas en el checkout registran `version`, `pow` y un ejemplo de excepción.

`command($name, $handler)` acepta un callback o una cadena `Controller@method`; si no se especifica método, el despacho usa `index`. Una clase sin namespace se resuelve bajo los controladores de la aplicación y una cadena con namespace completo se usa como FQCN.

```php
CliRouter::command('pow', function ($num, $exp) {
    return pow($num, $exp);
});

CliRouter::group('tools', function () {
    CliRouter::command('version', function () {
        return 'SimpleRest';
    });
});
```

El registro y los callbacks corresponden al código fuente; en esta revisión sólo se ejecutaron pantallas de ayuda de `php com`, sin invocar ninguna ruta registrada en `CliRouter`. `group()` combina prefijos con `:` y admite anidamiento. Para el grupo anterior, el nombre registrado queda `tools:version`.

## Resolución y argumentos

`resolve()` lee los argumentos de `$argv`. Busca desde la cadena más larga hasta la más corta, convirtiendo los tokens de comando en una clave separada por `:`; por ejemplo, los primeros tokens `tools version` se intentan como `tools:version`. Los tokens posteriores al comando se pasan como argumentos posicionales al callback o método.

`where($patterns)` guarda expresiones para los argumentos del comando actual. Para callbacks, CliRouter obtiene los nombres de parámetros con Reflection y compara cada valor proporcionado con una regex anclada. La rama de handler string intenta inspeccionar `Controller@method`; no se afirma el mismo contrato para strings sin `@method`. No hay coerción de tipo documentada por este camino.

Si una ruta registrada apunta a un paquete con `console_router` deshabilitado, `resolve()` retorna `false`. Si no hay ruta registrada, cae al estilo front controller de CLI: intenta una clase `{Controlador}Controller` de la aplicación y la acción indicada; cuando no encuentra el controlador, retorna `false` al caller para permitir la siguiente rama global. Las instrucciones del orden de resolvers pertenecen a [Arquitectura](../architecture.md).

Las rutas CLI encontradas envían el resultado mediante `Response::flush()`, que es terminal. El fallback de controller selecciona formato de salida según el tipo de controlador y la configuración presente en código; no se presenta aquí una matriz de formatos reproducida.

## Métodos cuyo contrato no está implementado

`compile()` está vacío en esta versión. `alias($name)` y `name($name)` guardan valores en un mapa de alias, pero `resolve()` busca únicamente en las rutas registradas y no consulta ese mapa. No dependas de alias o nombres CLI hasta que haya una implementación verificada. La evidencia fuente está en el [ledger de routing](../audit/README.md#claim-level-findings-routing-apis).

La clase incluye `getInstance`, `resolve`, `command`, `group`, `where`, `alias`, `name` y `compile`. No se encontró una suite dedicada a `CliRouter` en el conjunto de tests consultado; las rutas y el comportamiento descritos se trazaron en código, no se ejecutaron.

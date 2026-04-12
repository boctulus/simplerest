# Comparativa: Comandos en SimpleRest vs Laravel

## En Laravel, los comandos Artisan son bastante flexibles

Por defecto, Laravel coloca los comandos personalizados en:

```
app/Console/Commands/
```

Y los registra en:

```php
// app/Console/Kernel.php
protected $commands = [
    \App\Console\Commands\ZippyCommand::class,
];
```

Pero **esto no es obligatorio**. Laravel te permite definir comandos en cualquier lugar del proyecto o incluso dentro de **packages externos o internos**, gracias a su sistema de *service providers* y *auto-discovery*.

---

## Escenario 1: Comandos dentro de `app/Console/Commands`

El caso más simple:
- Guardás tus comandos ahí.
- Los registrás en el `Kernel` como en el ejemplo anterior.

Esto es lo que Laravel genera por defecto con `php artisan make:command`.

---

## Escenario 2: Comandos dentro de un package (local o externo)

Laravel permite definir comandos dentro de un package, por ejemplo:

```
packages/
  myvendor/
    mypackage/
      src/
        Console/
          Commands/
            ZippyCommand.php
      src/MyPackageServiceProvider.php
```

Y dentro del `MyPackageServiceProvider`, registrás los comandos así:

```php
public function register()
{
    $this->commands([
        \MyVendor\MyPackage\Console\Commands\ZippyCommand::class,
    ]);
}
```

Cuando el package se carga, el comando queda disponible en `artisan` **sin copiarlo a `app/`**.

> ✅ Es decir: **no hace falta copiar comandos a `app/` o `src/`**, Laravel los carga directamente desde el package si se declaran correctamente en el *service provider*.

---

## Escenario 3: Auto-discovery (para packages publicados en Composer)

Si el package usa *composer.json* con `"extra": {"laravel": {"providers": [...]}}`, ni siquiera tenés que registrar manualmente el service provider.  
Laravel lo descubre automáticamente y tus comandos se registran solos.

---

## En resumen

| Situación | Ubicación típica | Registro |
|------------|------------------|-----------|
| Proyecto base | `app/Console/Commands` | En `app/Console/Kernel.php` |
| Package local | `packages/Vendor/Package/src/Console/Commands` | En el Service Provider del package |
| Package distribuido (Composer) | Igual que el anterior | Auto-discovery via `composer.json` |

---

## En SimpleRest (comparando con lo tuyo)

Tu ruta actual:

```
app/Commands/ZippyCommand.php
```

es análoga a `app/Console/Commands` de Laravel.  
Si quisieras dar un salto hacia la flexibilidad de Laravel, podrías adoptar algo como:

- `app/Providers/CommandServiceProvider.php`  
  para registrar comandos que estén en distintos módulos.
- Y permitir que **packages/módulos** declaren sus propios comandos al ser cargados.

---

## Conclusión

Laravel ofrece una arquitectura mucho más **modular y extensible** para comandos, gracias a su sistema de *service providers* y *auto-discovery*.  
SimpleRest puede inspirarse en este modelo para permitir que cada módulo o paquete tenga su propio espacio de comandos sin necesidad de copiarlos a `app/`.

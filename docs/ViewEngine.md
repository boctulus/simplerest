# View Engine — SimpleRest

## Descripción

Motor de vistas liviano con soporte de layouts, partials y caché.

**Archivo**: `src/framework/View.php` (233 líneas)

---

## Uso Básico

```php
use Boctulus\Simplerest\View;

// Renderizar vista
View::render('users.index', ['users' => $users]);

// Con layout
View::render('users.index', ['users' => $users], 'admin');
```

## Layouts

```php
// views/layouts/admin.php
<html>
<head>
    <title><?= $title ?></title>
    <?= View::css() ?>
</head>
<body>
    <?= View::content() ?>  <!-- Contenido de la vista hija -->
    <?= View::js() ?>
</body>
</html>
```

## Vistas

```
app/Views/
├── layouts/
│   └── admin.php
├── users/
│   ├── index.php
│   └── form.php
└── components/
    └── alert.php
```

## Assets (CSS/JS)

```php
// En el controlador
View::addCss('admin.css');
View::addJs('app.js');

// En el layout
echo View::css();  // Renderiza <link> tags
echo View::js();   // Renderiza <script> tags
```

## Partials

```php
// Incluir partial dentro de una vista
View::include('components.alert', ['type' => 'success', 'message' => 'OK']);
```

## View Caching

```php
// Cachear vista renderizada
View::render('users.index', $data, null, 3600);  // 1 hora
```

## Helper Global

```php
// Función global view()
view('users.index', ['users' => $users]);
```

## Ver También

- [`PagesTrait`](../src/framework/Traits/PagesTrait.php) — page rendering
- [`HTML-Form-builder.md`](./HTML-Form-builder.md) — formularios HTML
- [`Helpers.md`](./Helpers.md) — helpers globales

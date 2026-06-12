---
name: view-engine
description: Guide for the SimpleRest View Engine — layouts, partials, assets, caching, and the view() helper.
---

# View Engine Skill

Lightweight view engine with layout support, partials, and asset management.

## Basic Usage

```php
use Boctulus\Simplerest\View;

View::render('users.index', ['users' => $users]);
View::render('users.index', ['users' => $users], 'admin');  // with layout
```

## Directory Structure

```
app/Views/
├── layouts/
│   ├── admin.php
│   └── default.php
├── users/
│   ├── index.php
│   └── form.php
└── components/
    └── alert.php
```

## Layouts

```php
<!-- views/layouts/admin.php -->
<html>
<head>
    <title><?= $title ?></title>
    <?= View::css() ?>
</head>
<body>
    <?= View::content() ?>  <!-- Child view content -->
    <?= View::js() ?>
</body>
</html>
```

## Views

```php
<!-- views/users/index.php -->
<h1>Users</h1>
<ul>
<?php foreach ($users as $user): ?>
    <li><?= $user['name'] ?></li>
<?php endforeach; ?>
</ul>
```

## Assets (CSS/JS)

```php
// In controller
View::addCss('admin.css');
View::addJs('app.js');

// In layout
echo View::css();   // renders <link> tags
echo View::js();    // renders <script> tags
```

## Partials

```php
// Include partial inside a view
View::include('components.alert', ['type' => 'success', 'message' => 'OK']);
```

## View Caching

```php
// Cache rendered view for 1 hour
View::render('users.index', $data, null, 3600);
```

## Global Helper

```php
view('users.index', ['users' => $users]);   // same as View::render()
render('partials.header');                   // render without layout
```

## Common Pattern

```php
// Controller
class UserController extends Controller
{
    function index() {
        $users = DB::table('users')->get();
        View::addCss('users.css');
        View::addJs('users.js');
        View::render('users.index', ['users' => $users], 'admin');
    }
}
```

## See Also

- [`docs/ViewEngine.md`](../docs/ViewEngine.md) — full reference
- [`docs/HTML-Form-builder.md`](../docs/HTML-Form-builder.md) — form builder
- [`docs/HTML-Form-builder.AdminLTE.md`](../docs/HTML-Form-builder.AdminLTE.md) — AdminLTE forms
- `create-admin-page` skill — admin page creation

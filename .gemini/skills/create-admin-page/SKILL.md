---
name: create-admin-page
description: Guides the creation of a new admin panel page in SimpleRest, including page class, view file, and route registration using Bootstrap 5 + AdminLTE.
---

# Creating an Admin Page

## Overview

Admin pages use **AdminLTE 3 + Bootstrap 5** layout. Each page needs:
1. A **page class** → `app/Pages/admin/{PageName}.php`
2. A **view file** (optional) → `app/Views/admin/{page_name}.php`
3. A **route** → `config/routes.php`

## Step 1: Generate Skeleton

```bash
php com make page admin/{page-name}
```

Example:
```bash
php com make page admin/my-stats
```

Creates `app/Pages/admin/MyStats.php`.

## Step 2: Write the Page Class

**File:** `app/Pages/admin/{PageName}.php`

```php
<?php

namespace Boctulus\Simplerest\pages\admin;

class PageName
{
    public $tpl_params = [
        'title'     => 'Page Title',
        'page_name' => 'Page Name',
        'footer'    => 'Copyright &copy; ...'
    ];

    function index(){
        // Register extra CSS/JS
        js_file(asset('third_party/select2/select2.min.js'));

        ob_start();
        require __DIR__ . '/../../Views/admin/page_name.php';
        return ob_get_clean();
    }
}
```

### Template variables required by `adminlte_tpl.php`:

| Variable | Description |
|---|---|
| `$title` | `<title>` tag content |
| `$page_name` | Displayed in header & breadcrumb |
| `$footer` | Footer HTML content |
| `$content` | Page body HTML (injected by `render()`) |

## Step 3: Create a View File (optional)

**File:** `app/Views/admin/page_name.php`

Plain PHP file with HTML + JS. Common pattern:

```php
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3>My Page</h3></div>
        <div class="card-body">...</div>
    </div>
</div>

<script>
$(document).ready(function(){
    // jQuery code
});
</script>
```

## Step 4: Add Route

**File:** `config/routes.php`

```php
WebRouter::get('admin/page-name', function(){
    $page = new \Boctulus\Simplerest\pages\admin\PageName();
    $content = $page->index();
    render($content, 'templates/adminlte_tpl.php', $page->tpl_params);
});
```

Place before `admin/una-pagina`.

## Step 5: Add to Admin Menu (optional)

If `admin_menu_linked_pages` is set in `config/config.php`, add an entry:

```php
'admin_menu_linked_pages' => [
    'Group Name' => [
        ['label' => 'Link Text', 'url' => 'admin/page-name', 'icon' => 'fas fa-chart-bar'],
    ],
],
```

## Available Helpers

| Helper | Usage |
|---|---|
| `asset('path')` | URL to `public/assets/path` |
| `css_file($path)` | Register CSS in `<head>` |
| `js_file($path)` | Register JS in footer |
| `js($code)` | Inline JS |
| `base_url` (JS) | Base URL from `<base>` tag |
| `access_token` (localStorage) | JWT token for API calls |

## API Calls from JS

JWT token is in `localStorage.getItem('access_token')`:

```javascript
$.ajax({
    url: base_url + 'api/v1/endpoint',
    headers: {'Authorization': 'Bearer ' + localStorage.getItem('access_token')},
    success: function(data){ ... }
});
```

## Notes

- The template `adminlte_tpl.php` requires `Config` to be imported (already fixed if using recent version).
- `username()` and `logout()` JS functions must be defined if not already present (read JWT payload from `localStorage`).
- Use `composer dump-autoload` after creating new classes.

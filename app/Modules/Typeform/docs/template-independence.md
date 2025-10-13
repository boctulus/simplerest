# Template Independiente de WordPress

Este documento explica cómo se implementó un template completamente independiente del tema activo de WordPress para el módulo Typeform.

## Problema a Resolver

Por defecto, cuando WordPress renderiza contenido, utiliza el tema activo (template) que puede incluir headers, footers, sidebars y estilos que no queremos para un formulario de tipo typeform que necesita:
- Diseño fullscreen
- Control total sobre el HTML
- Assets específicos sin interferencias
- Funcionalidad independiente

## Solución Implementada

### 1. Template Standalone (typeform-standalone.php)

Se creó un template completamente independiente que:

```php
<?php
// NO usa wp_head() ni wp_footer()
// NO hereda estilos del tema
// Genera URLs de assets de forma independiente

// Generar URL base del plugin sin WordPress
$plugin_base_url = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__DIR__, 4));
$plugin_base_url = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($plugin_base_url, '/'), '/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Assets con URLs generadas independientemente -->
    <link rel="stylesheet" href="<?= $plugin_base_url ?>/app/modules/Typeform/assets/css/typeform.css">
</head>
<body class="typeform-page">
    <!-- Contenido del formulario -->
    <main>
        <?= $typeform_content ?>
    </main>
    
    <!-- Scripts al final del body -->
    <footer>
        <script src="<?= $plugin_base_url ?>/app/modules/Typeform/assets/js/typeform.js"></script>
    </footer>
</body>
</html>
```

### 2. Generación de URLs Independiente

**Método WordPress (dependiente):**
```php
plugins_url('assets/css/style.css', PLUGIN_FILE)
```

**Método Independiente (implementado):**
```php
// Calcular la ruta base del plugin
$plugin_base_url = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__DIR__, 4));
$plugin_base_url = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($plugin_base_url, '/'), '/');

// Generar URL de asset
$asset_url = $plugin_base_url . '/app/modules/Typeform/assets/css/typeform.css';
```

### 3. JavaScript Sin WordPress AJAX

**Método WordPress (dependiente):**
```javascript
const typeform_ajax = {
    ajaxurl: '/wp-admin/admin-ajax.php',
    nonce: 'wordpress_nonce',
    action: 'wp_ajax_action'
};
```

**Método Independiente (implementado):**
```javascript
const typeform_ajax = {
    ajaxurl: '/typeform/process',  // Ruta directa del plugin
    api_base_url: 'http://site.com/wp-content/plugins/efirma'
};
```

### 4. Controller con Template Selector

```php
class TypeformController {
    function get() {
        // Para rutas directas: template independiente
        return get_view(__DIR__ . '/../modules/Typeform/views/typeform-standalone.php', $data);
    }
}
```

### 5. Shortcode con Template WordPress

```php
class Typeform extends Shortcode {
    function render() {
        // Para uso en posts/páginas: template integrado con WordPress
        return get_view(__DIR__ . '/views/typeform.php', $data);
    }
}
```

## Ventajas de esta Implementación

### ✅ **Independencia Total**
- No depende del tema activo de WordPress
- No hereda estilos no deseados
- Control total sobre HTML y CSS

### ✅ **Flexibilidad**
- Puede usarse como página independiente (`/typeform`)
- Puede integrarse en contenido WordPress (shortcode)
- Mantiene la misma funcionalidad en ambos casos

### ✅ **Performance**
- No carga assets innecesarios del tema
- Solo carga lo que necesita el formulario
- Tiempo de carga optimizado

### ✅ **Mantenimiento**
- Un solo código base para ambos usos
- Fácil de actualizar y mantener
- Assets versionados independientemente

## Estructura de Archivos

```
Typeform/
├── views/
│   ├── typeform.php           # Para uso como shortcode (WordPress)
│   ├── typeform-standalone.php # Para acceso directo (independiente)
│   └── steps/                 # Vistas parciales compartidas
├── assets/
│   ├── css/                   # Estilos específicos del formulario
│   ├── js/                    # JavaScript sin dependencias WP
│   └── img/                   # Imágenes del formulario
└── docs/
    └── template-independence.md # Este documento
```

## Comparación de Acceso

| Método | URL | Template | Integración |
|--------|-----|----------|-------------|
| **Ruta Directa** | `/typeform` | `typeform-standalone.php` | Independiente |
| **Shortcode** | `/page-with-shortcode` | Tema WP + `typeform.php` | Integrado |

## Casos de Uso

### 🎯 **Ruta Directa** (`/typeform`)
- Landing page del formulario
- Enlaces externos
- Testing y desarrollo
- Máxima independencia visual

### 🎯 **Shortcode** (en posts/páginas)
- Integración con contenido existente
- Mantener navegación del sitio
- SEO integrado
- Consistencia visual con el sitio

## Configuración

La implementación es automática, pero se puede configurar:

```php
// config/config.php
return [
    'api_base_url' => Env::get('API_BASE_URL', base_url()),
    'ui' => [
        'background_image' => 'blue-pos.jpeg',  // Imagen de fondo
        'brand' => [
            'title' => 'Mi Marca',
            'subtitle' => 'Mi Subtítulo'
        ]
    ]
];
```

## Troubleshooting

### Problema: Assets no cargan
```php
// Verificar que la URL base se genera correctamente
var_dump($plugin_base_url);
// Debe mostrar algo como: http://sitio.com/wp-content/plugins/efirma
```

### Problema: JavaScript no funciona
```javascript
// Verificar que typeform_ajax está definido
console.log(typeform_ajax);
// Debe mostrar: {ajaxurl: "/typeform/process", api_base_url: "..."}
```

### Problema: Formulario no envía
- Verificar que las rutas estén configuradas en `config/routes.php`
- Verificar que el controller `TypeformController` existe
- Revisar logs en `/logs/` para errores PHP

Esta implementación proporciona la flexibilidad necesaria para usar el formulario tanto como página independiente como integrado en WordPress, manteniendo un código base único y fácil de mantener.
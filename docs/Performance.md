# Guía de Performance — SimpleRest

## Filosofía

SimpleRest está diseñado para ser **rápido por defecto**: bootstrap 3-10ms vs 300-500ms de Laravel. Sin embargo, hay estrategias adicionales para optimizar aún más.

---

## Benchmark Actual

| Operación | SimpleRest | Laravel (ref) |
|-----------|-----------|----------------|
| Bootstrap (vacio) | 3-10ms | 300-500ms |
| Request simple | 10-30ms | 400-800ms |
| Query con JOIN | 15-50ms | 100-300ms |

> **⚠️ Nota**: No hay benchmarks oficiales. Los valores son estimaciones del autor.

## Estrategias de Optimización

### 1. OPCache

PHP OPCache es la optimización más impactante:

```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

```bash
# CLI para limpiar OPCache
php com system opcache-clear
```

### 2. Query Caching

```php
// Cachear resultados de consultas pesadas
$users = DB::table('users')
    ->where('active', 1)
    ->cached(3600)  // 1 hora
    ->get();
```

### 3. Caché de Archivos

```php
use Boctulus\Simplerest\Libs\FileCache;

$cache = new FileCache();
$data = $cache->remember('heavy_report', 3600, function() {
    return DB::table('sales')->aggregate('sum(total)');
});
```

### 4. Vistas en Caché

```php
View::render('report', $data, null, 3600);
```

### 5. Swoole (Servidor HTTP)

SimpleRest puede ejecutarse sobre **Swoole** para evitar el overhead de arranque PHP por request.

### 6. CDN para Assets

Assets estáticos en `public/` deben servirse via CDN o nginx directamente.

## Configuración Recomendada

### Nginx

```nginx
server {
    listen 80;
    root /var/www/simplerest/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Cache estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Ver También

- [`Caching.md`](./Caching.md) — sistema de caché
- [`Deployment.md`](./Deployment.md) — configuración de servidor
- [`docs/_internal/etc/PERFORMANCE.txt`](./_internal/etc/PERFORMANCE.txt)

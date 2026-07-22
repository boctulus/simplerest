---
name: deployment
description: Guide for deploying SimpleRest to production with Nginx, Apache, Docker, and performance optimization.
---

# Deployment Skill

## Requirements

- PHP 8.1+
- Web server (Nginx recommended / Apache / Docker)
- Database (MySQL, PostgreSQL, etc.)
- Composer (optional in production)

## Nginx (Recommended)

```nginx
server {
    listen 80;
    server_name simplerest.local;
    root /var/www/simplerest/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2?)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Apache

```apache
<VirtualHost *:80>
    DocumentRoot "/var/www/simplerest/public"
    ServerName simplerest.local

    <Directory "/var/www/simplerest/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Requires `.htaccess` in `public/`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

## Docker

```yaml
# docker-compose.yml
version: '3'
services:
  app:
    image: php:8.2-fpm
    volumes:
      - .:/var/www/simplerest
    working_dir: /var/www/simplerest
  nginx:
    image: nginx:alpine
    volumes:
      - .:/var/www/simplerest
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
    ports:
      - "80:80"
    depends_on:
      - app
  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
    volumes:
      - db_data:/var/lib/mysql
volumes:
  db_data:
```

## Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Disable debug mode
- [ ] Enable OPCache
- [ ] Run `php com make acl --force` to compile ACL
- [ ] Run pending migrations: `php com migrate`
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Configure HTTPS (SSL certificate)
- [ ] Set up log rotation
- [ ] Configure backup strategy

## Performance Optimization

```bash
# Enable OPCache in php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=120

# Clear cache
php com system clear
```

## Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
JWT_SECRET=your-production-secret
JWT_REFRESH_SECRET=your-refresh-secret
```

## See Also

- [`docs/Deployment.md`](../docs/Deployment.md) — full deployment guide
- [`docs/Performance.md`](../docs/Performance.md) — optimization strategies
- [`docs/Performance-Benchmark.md`](../docs/Performance-Benchmark.md) — benchmarks

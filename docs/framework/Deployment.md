# Guía de Deployment — SimpleRest

## Requisitos

- PHP 8.1+
- Servidor web (Nginx / Apache / Docker)
- Base de datos (MySQL, PostgreSQL, etc.)
- Composer (opcional)

---

## Servidores Soportados

### Nginx (Recomendado)

```nginx
server {
    listen 80;
    server_name simplerest.local;
    root D:/laragon/www/simplerest/public;
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

### Apache

```apache
<VirtualHost *:80>
    DocumentRoot "D:/laragon/www/simplerest/public"
    ServerName simplerest.local
    
    <Directory "D:/laragon/www/simplerest/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Requiere `.htaccess` en `public/`:

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
  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: simplerest
```

## Configuración de Entorno

```bash
# .env
APP_URL=http://simplerest.local
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=main
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simplerest
DB_USERNAME=root
DB_PASSWORD=secret

JWT_SECRET=your-secret-key
JWT_REFRESH_SECRET=your-refresh-secret
```

## Pasos para Deploy

1. Clonar repositorio
2. Configurar `.env`
3. `composer install --no-dev`
4. Crear base de datos
5. `php com migrations migrate`
6. Configurar servidor web apuntando a `public/`
7. Verificar permisos de `storage/` y `logs/`

## Ver También

- [`QuickStart.md`](./QuickStart.md) — instalación inicial
- [`Performance.md`](./Performance.md) — optimización de servidor

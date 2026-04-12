# Quickstart — SimpleRest Framework

> Get a REST API running in under 5 minutes.

---

## Prerequisites

- **PHP 7.4 – 8.3** (8.4 coming soon)
- **Composer** (for dependencies)
- **MySQL** (or MariaDB)
- **Apache/Nginx** with `mod_rewrite` enabled

---

## 1. Installation

### Option A: Clone the repository

```bash
git clone https://github.com/boctulus/simplerest.git
cd simplerest
composer install
```

### Option B: Use as a Composer dependency

```bash
composer require boctulus/simplerest
```

### Option C: Local path (during development)

In your consuming project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/simplerest"
        }
    ],
    "require": {
        "boctulus/simplerest": "@dev"
    }
}
```

Then run:
```bash
composer install
```

---

## 2. Environment Setup

### Copy and configure `.env`

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
APP_NAME=MyAPI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://myapi.local

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=my_api_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Create the database

```sql
CREATE DATABASE my_api_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 3. Web Server Configuration

### Apache

Point your virtual host to the project root. The included `.htaccess` handles URL rewriting automatically.

Example virtual host:

```apache
<VirtualHost *:80>
    ServerName myapi.local
    DocumentRoot "D:/laragon/www/simplerest"

    <Directory "D:/laragon/www/simplerest">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx

```nginx
server {
    listen 80;
    server_name myapi.local;
    root /path/to/simplerest;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 4. Verify Installation

Open your browser and navigate to `http://myapi.local`. You should see the SimpleRest welcome page.

Test with curl:

```bash
curl http://myapi.local
```

---

## 5. Your First API Endpoint

### Create a table

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Generate a schema file

```bash
php com make schema products
```

This creates `app/Schemas/main/ProductsSchema.php` with your table definition.

### That's it — your API is ready!

SimpleRest **automatically creates REST endpoints** for every table with a schema. No controller code needed.

Test it:

```bash
# Create a product
curl -X POST http://myapi.local/api/products \
  -H "Content-Type: application/json" \
  -d '{"name":"Widget","price":9.99}'

# List all products
curl http://myapi.local/api/products

# Get a single product
curl http://myapi.local/api/products/1

# Update a product
curl -X PUT http://myapi.local/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{"name":"Super Widget","price":19.99}'

# Delete a product
curl -X DELETE http://myapi.local/api/products/1
```

### Advanced filtering (built-in)

```bash
# Products where price >= 10
curl "http://myapi.local/api/products?price[gteq]=10"

# Products where name contains "Widget"
curl "http://myapi.local/api/products?name[contains]=Widget"

# Get only specific fields
curl "http://myapi.local/api/products?fields=id,name"

# Pagination
curl "http://myapi.local/api/products?limit=10&offset=20"

# Aggregation
curl "http://myapi.local/api/products?aggregate=count(id),avg(price)"
```

---

## 6. Create a Custom Route

Open `config/routes.php` and add:

```php
WebRouter::get('/hello/{name}', function($name) {
    Response::json(['message' => "Hello, {$name}!"]);
});
```

Test it:

```bash
curl http://myapi.local/hello/World
# {"message":"Hello, World!"}
```

---

## 7. Generate a Controller (optional)

For custom logic beyond auto-endpoints:

```bash
php com make controller MyController
```

This creates `app/Controllers/MyController.php`. Edit it:

```php
<?php

namespace Boctulus\Simplerest\Controllers;

class MyController
{
    public function index()
    {
        response()->json(['status' => 'ok', 'time' => date('Y-m-d H:i:s')]);
    }
}
```

Register the route in `config/routes.php`:

```php
WebRouter::get('/status', 'MyController@index');
```

---

## 8. Set Up Authentication

SimpleRest includes JWT authentication out of the box.

### Generate auth tables

```bash
php com make migration create_users_table
```

Or use the included migrations:

```bash
php com sql migrate
```

### Register a user

```bash
curl -X POST http://myapi.local/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com","password":"secret123"}'
```

### Login and get JWT token

```bash
curl -X POST http://myapi.local/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"secret123"}'
```

Response:
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
}
```

### Use the token

```bash
curl http://myapi.local/api/me \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

---

## 9. CLI Commands

SimpleRest has a built-in CLI system. List available commands:

```bash
php com help
```

Common commands:

```bash
# Generate files
php com make controller Name
php com make model Name
php com make schema Name
php com make middleware Name
php com make command Name
php com make package Name

# Database
php com sql list-databases
php com sql list-connections
php com sql find "SELECT * FROM users LIMIT 5"

# ACL
php com make acl --force

# Documentation
php com doc generate
```

---

## 10. Next Steps

| Topic | Documentation |
|-------|--------------|
| Routing | [`docs/Routing.md`](./Routing.md) |
| Query Builder | [`docs/QueryBuilder.md`](./QueryBuilder.md) |
| ORM / Models | [`docs/ORM.md`](./ORM.md) |
| Middleware | [`docs/Middlewares.md`](./Middlewares.md) |
| ACL | [`docs/ACL.md`](./ACL.md) |
| API Client | [`docs/ApiClient.md`](./ApiClient.md) |
| CLI Commands | [`docs/CommandLine.md`](./CommandLine.md) |
| Philosophy | [`docs/SimpleRest-philosophy.md`](./SimpleRest-philosophy.md) |

---

## Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
```

### Routes not working
- Verify `config/config.php` has `'web_router' => true`
- Check `.htaccess` exists in the project root
- Enable `mod_rewrite` in Apache

### Database connection error
- Verify credentials in `.env`
- Ensure the database exists
- Check MySQL is running

### Permission errors
```bash
# Ensure storage directories are writable
chmod -R 755 storage/
chmod -R 755 logs/
```

---

**Author**: Pablo Bozzolo (boctulus) — Software Architect

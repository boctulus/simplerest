> **Legacy disposition:** retained under `docs/audit/pending/` as an audit input, not a current setup guide. Installation/bootstrap claims were reviewed in this phase: the repository dependency install completed in a filtered snapshot, the local-path consumer example failed on a transitive package, and neither repository-as-application startup nor public Composer installation was reproduced. See the [claim-level audit](../../README.md#claim-level-findings-installation-and-bootstrap). API, database, authentication, routing, and CLI instructions remain pending their ordered audit phases.

> **Audited claim:** The section 5 claim that `php com make schema products` alone makes the CRUD API ready is rejected. Its sample `/api/products` paths also omit the version required by the checked-in API configuration. A complete corrected workflow remains unverified; see the [claim-level audit](../../README.md#claim-level-findings-schema-generation-and-automatic-api).

# Quickstart — SimpleRest Framework

> Get a REST API running in under 5 minutes.

---

## Prerequisites

- **PHP 8.1 – 8.4**
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
DB_PASSWORD=
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
| Routing | [`Routing`](../../../framework/Routing.md) |
| Query Builder | [`QueryBuilder`](../../../framework/QueryBuilder.md) |
| ORM / Models | [legacy ORM notes (pending audit)](./ORM-STATUS.md) |
| Middleware | [`Middlewares`](../../../framework/Middlewares.md) |
| ACL | [`ACL`](../../../framework/ACL.md) |
| API Client | [`ApiClient`](../../../framework/ApiClient.md) |
| CLI Commands | [`CommandLine`](../../../framework/CommandLine.md) |
| Philosophy | [legacy philosophy (pending audit)](./SimpleRest-Philosophy.md) |

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

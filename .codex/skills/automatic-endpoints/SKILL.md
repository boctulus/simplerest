---
name: automatic-endpoints
description: Guide for SimpleRest's zero-config REST endpoint system — auto-generated CRUD from schemas with filtering, pagination, sorting, and ACL.
---

# Automatic Endpoints Skill

SimpleRest auto-generates RESTful endpoints from schema files. No controller code needed for standard CRUD.

## How It Works

```
Schema file exists → Auto-endpoint at /api/v1/{resource}
```

The FrontController acts as catch-all for unmatched routes, resolves the controller via naming convention (`products` → `ProductsController` → `ProductsModel` → `products` table).

## Auto-Generated Endpoints

Given a schema for `products`, these endpoints work immediately:

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/api/v1/products` | List records |
| GET | `/api/v1/products/{id}` | Show record |
| POST | `/api/v1/products` | Create record |
| PUT | `/api/v1/products/{id}` | Update record |
| PATCH | `/api/v1/products/{id}` | Partial update |
| DELETE | `/api/v1/products/{id}` | Delete record |

## Query Parameters (Filtering)

### Exact Match
```
GET /api/v1/products?name=Vodka
GET /api/v1/products?name=Vodka&size=1L
```

### IN / NOT IN
```
GET /api/v1/products?name=Vodka,Wisky,Tekila
GET /api/v1/products?name[in]=Vodka,Wisky
GET /api/v1/products?name[notIn]=CocaCola
```

### String Comparisons
```
GET /api/v1/products?name[contains]=jugo
GET /api/v1/products?name[startsWith]=Coca
GET /api/v1/products?name[endsWith]=Cola
```

### Numeric Comparisons
```
GET /api/v1/products?cost[gteq]=25&cost[lteq]=100
GET /api/v1/products?cost[gt]=50
GET /api/v1/products?cost[between]=200,300
```

### NULL / NOT NULL
```
GET /api/v1/products?description=NULL
GET /api/v1/products?description[neq]=NULL
```

## Field Selection

```
GET /api/v1/products?fields=id,name,cost
GET /api/v1/products?exclude=description,internal_notes
```

## Pagination

```
GET /api/v1/products?page=3
GET /api/v1/products?pageSize=20&page=2
GET /api/v1/products?limit=10&offset=40
```

> **Configurable:** Los nombres de los parámetros de paginación se definen en `config/config.php` → `paginator.params`. Por ejemplo, si el proyecto mapea `pageSize` → `size` y `page` → `page_num`, los endpoints esperarán `?size=20&page_num=2` en vez de los valores por defecto. También se configura `max_limit`, `default_limit` y el `formatter` de la respuesta.

## Sorting

```
GET /api/v1/products?order[cost]=DESC
GET /api/v1/products?order[cost]=DESC&order[name]=ASC
```

## Aggregates

```
GET /api/v1/products?props=min(cost)
GET /api/v1/products?size=1L&props=avg(cost)
GET /api/v1/products?props=count(*)
```

## Related / Sub-Resources

```
GET /api/v1/users/123/posts
GET /api/v1/users/123/posts?page=1
GET /api/v1/products?include=category
GET /api/v1/products?_related=category
```

## ACL Integration

- `list` — access to GET collection
- `show` — access to GET single record
- `create` — access to POST
- `update` — access to PUT/PATCH
- `delete` — access to DELETE
- `read_all` — see other users' records
- `write_all` — modify others' records

## Customizing Auto-Endpoints

Override methods in `app/Controllers/Api/{Resource}Controller.php`:

```php
class ProductsController extends MyApiController
{
    // Override before standard CRUD
    protected function beforeIndex() { ... }
    protected function beforeStore() { ... }

    // Add custom logic
    public function customMethod() { ... }
}
```

## Disabling Auto-Endpoints

Remove or rename the schema file, or set up a manual route that takes precedence.

## See Also

- [`docs/AutomaticEndpoints-Summary.md`](../docs/AutomaticEndpoints-Summary.md) — summary
- [`docs/SimpleRest-API-Rest.md`](../docs/SimpleRest-API-Rest.md) — REST query syntax
- [`docs/Schemas.md`](../docs/Schemas.md) — schema definition
- `create-api-endpoint-guide` skill — creating custom endpoints
- `schemas` skill — schema file creation
- `subresources` skill — nested CRUD

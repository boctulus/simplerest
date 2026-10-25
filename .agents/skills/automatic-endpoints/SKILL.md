---
name: automatic-endpoints
description: Generate and use schema-driven REST controllers with filtering, pagination, sorting, aggregation, relationships and ACL.
---

# Automatic Endpoints Skill

SimpleRest provides reusable CRUD methods through `ApiController`; you do not need to implement each REST action by hand. Generate the schema descriptor, model and API controller for an existing database table using the optional scaffolding package.

## How It Works

```bash
php com make any products --schema --model --api --from=main
```

The `make` command group comes from `boctulus/simplerest-scaffolding`, which must be enabled in `config/commands.php`. This generates PHP artifacts; it does not create the SQL table. `ApiHandler` resolves a controller by resource name, and `FrontController` requires that class to exist and checks its callable HTTP methods. The generated controller inherits the generic CRUD implementation.

Alternatively, run `php com make schema products`, `php com make model products` and `php com make api products` separately.

## Auto-Generated Endpoints

With a generated schema, model and API controller for `products`, these routes are available subject to the application's authentication and ACL:

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

Edit the generated controller under `app/Controllers/Api/`. Its template extends `Core\\Api\\ApiController`. Use actual hooks such as `onPostingBeforeCheck($id, array &$data)` or `onGot($id, ?int $count)`, or override the supported HTTP methods with their existing signatures.

## Disabling Auto-Endpoints

Remove or unregister the generated API controller to stop that resource route resolving, or restrict its actions through ACL. Removing the schema descriptor alone does not unregister an existing controller.

## See Also

- [Automatic API endpoints](../../../docs/api/automatic-endpoints.md) — controller resolution and query parameters
- [Schema descriptors](../../../docs/database/schemas.md) — schema generation
- [Scaffolding](../../../docs/packages/scaffolding.md) — installing and enabling `make` commands
- `create-api-endpoint-guide` skill — creating custom endpoints
- `schemas` skill — schema file creation
- `subresources` skill — nested CRUD

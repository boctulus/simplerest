---
name: subresources
description: Guide for using nested CRUD endpoints (SubResources) in SimpleRest, automatically inferred from schema relationships.
---

# SubResources Skill

SubResources expose **nested CRUD operations** from schema relationships without writing controller code.

## How It Works

Given a relationship in the schema, endpoints are auto-generated at:

```
/api/v1/{parent}/{parentId}/{child}
```

## Schema Setup

Define relationships in `app/Schemas/main/users.php`:

```php
'relationships' => [
    'posts'   => [['users.id', 'posts.user_id']],         // 1:N
    'profile' => [['users.id', 'profiles.user_id']],       // 1:1
],
```

## Auto-Generated Endpoints

With the above schema, these endpoints work automatically:

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/api/v1/users/{id}/posts` | List user's posts |
| GET | `/api/v1/users/{id}/posts/{postId}` | Get specific post |
| POST | `/api/v1/users/{id}/posts` | Create post for user |
| PUT | `/api/v1/users/{id}/posts/{postId}` | Update post |
| PATCH | `/api/v1/users/{id}/posts/{postId}` | Partial update |
| DELETE | `/api/v1/users/{id}/posts/{postId}` | Delete post |

## Relationship Types

| Type | Example | Behavior |
|------|---------|----------|
| 1:N | user → posts | Returns array of children |
| 1:1 | user → profile | Returns single child object |
| N:M | post → tags (pivot) | List via pivot table |

## Insert with Sub-Resources

Create parent + children in one operation:

```php
$data = [
    'name'   => 'Juan',
    'email'  => 'juan@example.com',
    'posts'  => [
        ['title' => 'Post 1', 'body' => '...'],
        ['title' => 'Post 2', 'body' => '...'],
    ],
    'profile' => [
        'bio'   => 'Developer',
        'avatar' => 'avatar.jpg',
    ],
];

$model = new User(true);
$model->insertWithSubResources($data);
```

## Query Parameters on Sub-Resources

All standard REST query parameters work on sub-resource endpoints:

```
GET /api/v1/users/123/posts?page=1&pageSize=10
GET /api/v1/users/123/posts?order[created_at]=DESC
GET /api/v1/users/123/posts?fields=id,title
```

## Traits Used

- `SubResourceHandler` — enables sub-resource routing
- `InsertWithSubResourcesTrait` — enables nested inserts

## Key Points

- Sub-resources require **schema relationship definitions** only
- No controller code needed for standard CRUD
- Works with all schema connections (multi-tenant)
- Query parameters (filter, sort, paginate) are supported
- N:M requires pivot table in relationship definition

## See Also

- [`docs/SubResources.md`](../docs/SubResources.md) — full reference
- [`docs/Schemas.md`](../docs/Schemas.md) — defining relationships
- [`docs/AutoJoins.md`](../docs/AutoJoins.md) — automatic joins
- `schemas` skill — creating schema files

# Sub-Resources — SimpleRest

## Descripción

Los **Sub-Resources** permiten operaciones CRUD sobre recursos anidados, inferidos automáticamente desde las relaciones definidas en los schemas.

**Traits**: `SubResourceHandler`, `InsertWithSubResourcesTrait`  
**Ruta**: `/api/v1/{parent}/{parentId}/{child}`

---

## Uso

Dado un schema con relaciones:

```php
// app/Schemas/main/users.php
'relationships' => [
    'posts' => [['users.id', 'posts.user_id']],
    'profile' => [['users.id', 'profiles.user_id']],
],
```

El endpoint `/api/v1/users/123/posts` expone automáticamente:

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/v1/users/{id}/posts` | Listar posts del usuario |
| GET | `/api/v1/users/{id}/posts/{postId}` | Obtener post específico |
| POST | `/api/v1/users/{id}/posts` | Crear post para el usuario |
| PUT | `/api/v1/users/{id}/posts/{postId}` | Actualizar post |
| PATCH | `/api/v1/users/{id}/posts/{postId}` | Actualizar parcialmente |
| DELETE | `/api/v1/users/{id}/posts/{postId}` | Eliminar post |

## Tipos de Relación

| Tipo | Ejemplo | Comportamiento |
|------|---------|----------------|
| 1:N | user → posts | Lista de hijos |
| 1:1 | user → profile | Objeto único hijo |
| N:M | post → tags (pivot) | Lista vía tabla pivot |

## Insert con Sub-Resources

```php
$data = [
    'name' => 'Juan',
    'email' => 'juan@example.com',
    'posts' => [
        ['title' => 'Post 1', 'body' => '...'],
        ['title' => 'Post 2', 'body' => '...'],
    ],
    'profile' => [
        'bio' => 'Developer',
        'avatar' => 'avatar.jpg',
    ],
];

$model = new User(true);
$model->insertWithSubResources($data);
```

Esto crea el usuario y sus posts/profile en una sola operación.

## Ver También

- [`Schemas.md`](./Schemas.md) — definición de relaciones
- [`AutoJoins.md`](./AutoJoins.md) — joins automáticos
- [`AutomaticEndpoints-Summary.md`](./AutomaticEndpoints-Summary.md)

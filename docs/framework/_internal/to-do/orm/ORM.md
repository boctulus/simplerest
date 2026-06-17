# ORM en SimpleRest

El ORM (Object-Relational Mapping) de SimpleRest proporciona una interfaz orientada a objetos para interactuar con bases de datos, combinando la potencia del Query Builder con la simplicidad de patrones Active Record.

## Tabla de Contenidos

- [Introducción](#introducción)
- [Filosofía del ORM](#filosofía-del-orm)
- [Configuración Básica](#configuración-básica)
- [Trabajando con Modelos](#trabajando-con-modelos)
  - [Modo Tradicional (Instancias)](#modo-tradicional-instancias)
  - [Modo Laravel-like (Métodos Estáticos)](#modo-laravel-like-métodos-estáticos)
- [Operaciones CRUD](#operaciones-crud)
- [Consultas](#consultas)
- [Relaciones](#relaciones)
- [Hooks y Eventos](#hooks-y-eventos)
- [Mutadores](#mutadores)
- [Validación](#validación)
- [Transacciones](#transacciones)
- [Mejores Prácticas](#mejores-prácticas)
- [Ejemplos Avanzados](#ejemplos-avanzados)

---

## Introducción

El ORM de SimpleRest ofrece dos estilos de trabajo:

1. **Tradicional**: Usando instancias del modelo y el Query Builder completo
2. **Laravel-like**: Usando métodos estáticos al estilo Active Record

Ambos estilos se pueden combinar según las necesidades de tu aplicación.

```php
// Estilo tradicional
$model = new Brand(true);
$results = $model->where('active', 1)->get();

// Estilo Laravel-like
$results = Brand::where('active', 1)->get();
```

---

## Filosofía del ORM

El ORM de SimpleRest se basa en varios principios:

### 1. **Compatibilidad con Laravel**
Los métodos y sintaxis son altamente compatibles con Laravel para facilitar la migración y aprendizaje.

### 2. **Performance First**
Uso intensivo de caching interno y optimizaciones para consultas rápidas.

### 3. **Multi-Tenant**
Diseñado desde cero para soportar múltiples conexiones y bases de datos.

### 4. **Flexibilidad**
Combina lo mejor del Query Builder con patrones ORM sin imponer restricciones.

### 5. **Transparencia**
Acceso completo al SQL generado y capacidad de usar SQL raw cuando sea necesario.

---

## Configuración Básica

### Estructura de Directorios

```
app/
├── Models/              # Modelos de la aplicación principal
│   └── UserModel.php
packages/
└── boctulus/
    └── zippy/
        └── src/
            └── Models/  # Modelos del package
                ├── Brand.php
                └── BrandCategory.php
```

### Crear un Modelo

**Archivo: `app/Models/User.php`**

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

class User extends Model
{
    // Tabla de la base de datos
    protected static $table = 'users';

    // Campos ocultos (no se incluyen en respuestas JSON)
    protected $hidden = ['password', 'api_token'];

    // Campos no rellenables (protegidos contra mass assignment)
    protected $not_fillable = ['id', 'created_at'];

    // Aliases para nombres de campos
    protected $field_names = [
        'nombre' => 'name',
        'correo' => 'email',
    ];

    // Formateadores personalizados
    protected $formatters = [];

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);

        // Configurar nombre de tabla
        $this->table_name = 'users';

        // Si usa una conexión específica
        // $this->setConn(\Boctulus\Simplerest\Core\Libs\DB::getConnection('main'));
    }
}
```

---

## Trabajando con Modelos

### Modo Tradicional (Instancias)

Este es el modo más potente, con acceso completo al Query Builder.

```php
<?php

use Boctulus\Simplerest\Models\User;

// Crear instancia conectada a la BD
$userModel = new User(true);

// Consultas
$users = $userModel->where('active', 1)
                   ->orderBy('created_at', 'DESC')
                   ->limit(10)
                   ->get();

// Inserción
$userId = $userModel->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_BCRYPT),
]);

// Actualización
$affected = $userModel->where('id', $userId)
                      ->update(['name' => 'Jane Doe']);

// Eliminación
$deleted = $userModel->where('id', $userId)->delete();

// Contar
$total = $userModel->where('active', 1)->count();

// Verificar existencia
$exists = $userModel->where('email', 'john@example.com')->exists();
```

### Modo Laravel-like (Métodos Estáticos)

Más limpio para consultas simples.

```php
<?php

use Boctulus\Simplerest\Models\User;

// Consultas simples
$user = User::where('email', 'john@example.com')->first();

// Búsqueda por ID
$user = User::findOrFail(123);

// Crear registro
$userId = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Verificar existencia
$exists = User::where('email', 'john@example.com')->exists();
```

**⚠️ Limitaciones del Modo Estático:**

- No todos los métodos del Query Builder están disponibles como estáticos
- Para consultas complejas, usa el modo tradicional
- Los resultados de `first()` y `get()` son arrays, no objetos

Ver [ORM-Laravel-like.md](ORM-Laravel-like.md) para más detalles.

---

## Operaciones CRUD

### Create (Crear)

```php
use Boctulus\Simplerest\Models\User;

// Crear un registro
$userId = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
    'active' => true,
]);

echo "Usuario creado con ID: $userId";

// Crear múltiples registros
User::create([
    ['name' => 'Alice', 'email' => 'alice@example.com'],
    ['name' => 'Bob', 'email' => 'bob@example.com'],
    ['name' => 'Charlie', 'email' => 'charlie@example.com'],
]);

// Crear o ignorar duplicados
$userId = User::create([
    'email' => 'john@example.com',
    'name' => 'John Doe',
], true); // ignore_duplicates = true
```

### Read (Leer)

```php
// Obtener todos los registros
$users = User::where('active', 1)->get();

// Obtener primer registro
$user = User::where('email', 'john@example.com')->first();

// Buscar por ID
$user = User::findOrFail(123);

// Obtener solo ciertos campos
$userModel = new User(true);
$users = $userModel->select(['id', 'name', 'email'])
                   ->where('active', 1)
                   ->get();

// Con paginación
$userModel = new User(true);
$paginated = $userModel->where('active', 1)
                       ->paginate(15); // 15 por página

// Ordenamiento
$users = User::where('active', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();
```

### Update (Actualizar)

```php
use Boctulus\Simplerest\Models\User;

// Actualizar con instancia
$userModel = new User(true);
$affected = $userModel->where('id', 123)
                      ->update([
                          'name' => 'Jane Doe',
                          'updated_at' => date('Y-m-d H:i:s'),
                      ]);

echo "Registros actualizados: $affected";

// Actualizar múltiples registros
$userModel = new User(true);
$affected = $userModel->where('active', 0)
                      ->update(['status' => 'inactive']);

// Incrementar/Decrementar
$userModel = new User(true);
$userModel->where('id', 123)->increment('login_count');
$userModel->where('id', 123)->decrement('credits', 10);
```

### Delete (Eliminar)

```php
use Boctulus\Simplerest\Models\User;

// Eliminar un registro
$userModel = new User(true);
$deleted = $userModel->where('id', 123)->delete();

// Eliminar múltiples registros
$userModel = new User(true);
$deleted = $userModel->where('active', 0)
                     ->where('created_at', '<', '2020-01-01')
                     ->delete();

echo "Registros eliminados: $deleted";

// Soft Delete (si está configurado)
$userModel = new User(true);
$userModel->where('id', 123)->delete(); // Marca deleted_at

// Hard Delete (eliminar permanentemente)
$userModel = new User(true);
$userModel->where('id', 123)->hardDelete();

// Restaurar soft deleted
$userModel = new User(true);
$userModel->where('id', 123)->restore();
```

---

## Consultas

### Consultas Básicas

```php
use Boctulus\Simplerest\Models\User;

// WHERE simple
$users = User::where('active', 1)->get();

// WHERE con operador
$users = User::where('age', '>', 18)->get();

// Múltiples WHERE (AND)
$users = User::where('active', 1)
            ->where('age', '>', 18)
            ->get();

// WHERE con array
$users = User::where([
    ['active', 1],
    ['age', '>', 18],
    ['role', 'admin'],
])->get();

// OR WHERE
$userModel = new User(true);
$users = $userModel->where('role', 'admin')
                   ->orWhere('role', 'moderator')
                   ->get();
```

### Consultas Avanzadas

```php
use Boctulus\Simplerest\Models\User;

$userModel = new User(true);

// WHERE IN
$users = $userModel->whereIn('id', [1, 2, 3, 4, 5])->get();

// WHERE NOT IN
$users = $userModel->whereNotIn('role', ['banned', 'suspended'])->get();

// WHERE NULL
$users = $userModel->whereNull('deleted_at')->get();

// WHERE NOT NULL
$users = $userModel->whereNotNull('email_verified_at')->get();

// WHERE BETWEEN
$users = $userModel->whereBetween('age', [18, 65])->get();

// WHERE LIKE
$users = $userModel->whereLike('name', '%John%')->get();

// WHERE con subconsulta
$users = $userModel->whereRaw('age > (SELECT AVG(age) FROM users)')->get();

// Agrupación de condiciones
$users = $userModel->where('active', 1)
                   ->where(function($query) {
                       $query->where('role', 'admin')
                             ->orWhere('role', 'moderator');
                   })
                   ->get();
```

### Agregaciones

```php
use Boctulus\Simplerest\Models\User;

$userModel = new User(true);

// COUNT
$total = $userModel->where('active', 1)->count();

// MAX
$maxAge = $userModel->max('age');

// MIN
$minAge = $userModel->min('age');

// AVG
$avgAge = $userModel->avg('age');

// SUM
$totalCredits = $userModel->sum('credits');

// GROUP BY con HAVING
$stats = $userModel->select(['role', 'COUNT(*) as total'])
                   ->groupBy('role')
                   ->having('total', '>', 10)
                   ->get();
```

### Joins

```php
use Boctulus\Simplerest\Models\User;

$userModel = new User(true);

// INNER JOIN
$results = $userModel->join('profiles', 'users.id', 'profiles.user_id')
                     ->select(['users.*', 'profiles.bio'])
                     ->where('users.active', 1)
                     ->get();

// LEFT JOIN
$results = $userModel->leftJoin('profiles', 'users.id', 'profiles.user_id')
                     ->select(['users.*', 'profiles.bio'])
                     ->get();

// Múltiples joins
$results = $userModel->join('profiles', 'users.id', 'profiles.user_id')
                     ->join('roles', 'users.role_id', 'roles.id')
                     ->select(['users.*', 'profiles.bio', 'roles.name as role_name'])
                     ->get();
```

---

## Relaciones

SimpleRest tiene un sistema de relaciones automáticas cuando se utilizan **schemas**. El framework detecta automáticamente relaciones 1:1, 1:n, n:1 y n:m basándose en las claves foráneas definidas en los schemas.

### Relaciones Automáticas con Schemas

Cuando defines un schema con relaciones, SimpleRest detecta y maneja automáticamente las relaciones entre tablas.

**Ejemplo de Schema con Relaciones:**

```php
<?php

namespace Boctulus\Simplerest\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

class UserSchema implements ISchema
{
    static function get(){
        return [
            'table_name' => 'users',
            'id_name' => 'id',
            'fields' => ['id', 'username', 'email', 'created_at'],

            'attr_types' => [
                'id' => 'INT',
                'username' => 'STR',
                'email' => 'STR',
                'created_at' => 'STR'
            ],

            'primary' => ['id'],
            'autoincrement' => 'id',
            'nullable' => ['created_at'],
            'required' => ['username', 'email'],

            // Claves foráneas
            'fks' => [],

            // Definir relaciones
            'relationships' => [
                'posts' => [
                    ['users.id', 'posts.user_id']  // users tiene muchos posts
                ],
                'profile' => [
                    ['users.id', 'profiles.user_id']  // users tiene un profile
                ]
            ],

            'expanded_relationships' => [
                'posts' => [
                    [
                        ['users', 'id'],
                        ['posts', 'user_id']
                    ]
                ],
                'profile' => [
                    [
                        ['users', 'id'],
                        ['profiles', 'user_id']
                    ]
                ]
            ],
        ];
    }
}
```

**Schema de la tabla relacionada (Posts):**

```php
<?php

namespace Boctulus\Simplerest\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

class PostSchema implements ISchema
{
    static function get(){
        return [
            'table_name' => 'posts',
            'id_name' => 'id',
            'fields' => ['id', 'user_id', 'title', 'content', 'created_at'],

            'attr_types' => [
                'id' => 'INT',
                'user_id' => 'INT',
                'title' => 'STR',
                'content' => 'TEXT',
                'created_at' => 'STR'
            ],

            'primary' => ['id'],
            'autoincrement' => 'id',

            // Clave foránea
            'fks' => ['user_id'],

            // Relación inversa
            'relationships' => [
                'users' => [
                    ['users.id', 'posts.user_id']
                ]
            ],

            'expanded_relationships' => [
                'users' => [
                    [
                        ['users', 'id'],
                        ['posts', 'user_id']
                    ]
                ]
            ],
        ];
    }
}
```

### Usar Relaciones en el Modelo

Una vez definidos los schemas, activa las relaciones en el modelo:

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Schemas\UserSchema;

class User extends Model
{
    protected static $table = 'users';

    function __construct(bool $connect = false)
    {
        // ⚠️ IMPORTANTE: Pasar el schema al constructor
        parent::__construct($connect, UserSchema::class);

        $this->table_name = 'users';
    }
}
```

### Cargar Relaciones (Eager Loading)

```php
use Boctulus\Simplerest\Models\User;

$userModel = new User(true);

// Cargar usuarios con sus posts
$users = $userModel->connectTo('posts')
                   ->get();

// Resultado incluye posts automáticamente:
/*
[
    {
        "id": 1,
        "username": "john",
        "email": "john@example.com",
        "posts": [
            {"id": 1, "title": "Post 1", "content": "..."},
            {"id": 2, "title": "Post 2", "content": "..."}
        ]
    },
    ...
]
*/

// Cargar múltiples relaciones
$users = $userModel->connectTo(['posts', 'profile'])
                   ->get();
```

### Tipos de Relaciones

El framework detecta automáticamente el tipo de relación:

#### 1. Relación 1:1 (hasOne / belongsTo)

```php
// users.id -> profiles.user_id (1:1)
$userModel = new User(true);
$users = $userModel->connectTo('profile')->get();
```

#### 2. Relación 1:N (hasMany)

```php
// users.id -> posts.user_id (1:n)
$userModel = new User(true);
$users = $userModel->connectTo('posts')->get();
```

#### 3. Relación N:M (belongsToMany)

Para relaciones muchos a muchos, define una tabla pivot:

```php
// users <-> roles (a través de user_roles)
'relationships' => [
    'roles' => [
        ['users.id', 'user_roles.user_id'],
        ['user_roles.role_id', 'roles.id']
    ]
]
```

### Verificar Tipo de Relación

```php
use Boctulus\Simplerest\Core\Model;

// Verificar si existe relación 1:1
$is11 = Model::is11('users', 'profiles');

// Verificar si existe relación 1:n
$is1N = Model::is1N('users', 'posts');

// Verificar si existe relación n:m
$isNM = Model::isNM('users', 'roles');

// Obtener tipo de relación
$type = Model::getRelType('users', 'posts');
// Retorna: '1:n', 'n:1', 'n:m', o '1:1'
```

### Generar Schemas Automáticamente

SimpleRest puede generar schemas automáticamente desde la base de datos:

```bash
# Generar schema para una tabla
php com make schema users

# Generar schema con conexión específica
php com make schema brands --connection=zippy
```

El comando analiza las foreign keys y genera automáticamente las definiciones de relaciones.

### Relaciones Sin Schemas (Manual)

Si no usas schemas, puedes implementar relaciones manualmente:

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

class User extends Model
{
    protected static $table = 'users';

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'users';
    }

    /**
     * Obtener el perfil del usuario
     */
    public function profile()
    {
        if (!isset($this->orm_attributes['id'])) {
            return null;
        }

        $profileModel = new Profile(true);
        return $profileModel->where('user_id', $this->orm_attributes['id'])->first();
    }

    /**
     * Obtener los posts del usuario
     */
    public function posts()
    {
        if (!isset($this->orm_attributes['id'])) {
            return [];
        }

        $postModel = new Post(true);
        return $postModel->where('user_id', $this->orm_attributes['id'])->get();
    }

    /**
     * Obtener con posts usando JOIN
     */
    public static function withPosts()
    {
        $instance = new static(true);
        return $instance->leftJoin('posts', 'users.id', 'posts.user_id')
                       ->select(['users.*', 'posts.title as post_title'])
                       ->get();
    }
}
```

**Uso de relaciones manuales:**

```php
// Usar método helper
$user = User::findOrFail(123);
$profile = $user->profile();
$posts = $user->posts();

// Usar JOIN
$usersWithPosts = User::withPosts();
```

---

## Hooks y Eventos

El ORM proporciona hooks para interceptar operaciones:

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

class User extends Model
{
    protected static $table = 'users';

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'users';
    }

    /**
     * Antes de crear
     */
    protected function onCreating(Array &$data)
    {
        // Hash password antes de insertar
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Agregar timestamp si no existe
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        // Logging
        error_log("Creating user: " . json_encode($data));
    }

    /**
     * Después de crear
     */
    protected function onCreated(Array &$data, $last_inserted_id)
    {
        // Enviar email de bienvenida
        // $this->sendWelcomeEmail($data['email']);

        error_log("User created with ID: $last_inserted_id");
    }

    /**
     * Antes de actualizar
     */
    protected function onUpdating(Array &$data)
    {
        // Actualizar timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Validar cambios
        if (isset($data['email'])) {
            // Verificar que el email no esté en uso
        }
    }

    /**
     * Después de actualizar
     */
    protected function onUpdated(Array &$data, ?int $count)
    {
        error_log("Updated $count user(s)");
    }

    /**
     * Antes de eliminar
     */
    protected function onDeleting(Array &$data)
    {
        error_log("Deleting user: " . json_encode($data));
    }

    /**
     * Después de eliminar
     */
    protected function onDeleted(Array &$data, ?int $count)
    {
        error_log("Deleted $count user(s)");
    }

    /**
     * Antes de leer
     */
    protected function onReading()
    {
        // Logging de lecturas
    }

    /**
     * Después de leer
     */
    protected function onRead(int $count)
    {
        error_log("Read $count record(s)");
    }
}
```

**Hooks disponibles:**

- `onCreating(&$data)` - Antes de insertar
- `onCreated(&$data, $id)` - Después de insertar
- `onUpdating(&$data)` - Antes de actualizar
- `onUpdated(&$data, $count)` - Después de actualizar
- `onDeleting(&$data)` - Antes de eliminar
- `onDeleted(&$data, $count)` - Después de eliminar
- `onReading()` - Antes de leer
- `onRead($count)` - Después de leer
- `onRestoring(&$data)` - Antes de restaurar (soft delete)
- `onRestored(&$data, $count)` - Después de restaurar
- `boot()` - Al inicializar el modelo
- `init()` - Después de la construcción

---

## Mutadores

Los mutadores transforman datos antes de guardar o después de leer:

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

class User extends Model
{
    protected static $table = 'users';

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'users';

        // Configurar mutadores
        $this->setInputMutator('email', function($value) {
            return strtolower(trim($value));
        });

        $this->setOutputMutator('name', function($value) {
            return ucwords($value);
        });

        $this->setOutputMutator('created_at', function($value) {
            return date('d/m/Y H:i', strtotime($value));
        });
    }
}
```

**Uso:**

```php
// Los mutadores se aplican automáticamente
$userId = User::create([
    'email' => '  JOHN@EXAMPLE.COM  ', // Se guarda como 'john@example.com'
    'name' => 'john doe',
]);

$user = User::findOrFail($userId);
echo $user->name; // Output: "John Doe" (aplicó ucwords)
```

---

## Validación

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\Validator;

class User extends Model
{
    protected static $table = 'users';

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'users';

        // Configurar reglas de validación
        $this->schema['rules'] = [
            'name' => [
                'type' => 'string',
                'required' => true,
                'min' => 3,
                'max' => 100,
            ],
            'email' => [
                'type' => 'email',
                'required' => true,
            ],
            'age' => [
                'type' => 'int',
                'min' => 18,
                'max' => 120,
            ],
        ];

        // Configurar validador
        $this->setValidator(new Validator());
    }
}
```

**Uso:**

```php
use Boctulus\Simplerest\Core\Exceptions\InvalidValidationException;

try {
    User::create([
        'name' => 'Jo', // Muy corto
        'email' => 'invalid-email', // Email inválido
        'age' => 15, // Menor de edad
    ]);
} catch (InvalidValidationException $e) {
    $errors = json_decode($e->getMessage(), true);
    print_r($errors);
    /*
    Array (
        [name] => Name must be at least 3 characters
        [email] => Invalid email format
        [age] => Age must be at least 18
    )
    */
}
```

---

## Transacciones

```php
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Models\User;
use Boctulus\Simplerest\Models\Profile;

DB::beginTransaction();

try {
    // Crear usuario
    $userId = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    // Crear perfil
    $profileModel = new Profile(true);
    $profileModel->create([
        'user_id' => $userId,
        'bio' => 'Software developer',
        'website' => 'https://example.com',
    ]);

    // Si todo salió bien, commit
    DB::commit();

    echo "Usuario y perfil creados exitosamente";

} catch (Exception $e) {
    // Si algo falló, rollback
    DB::rollback();

    error_log("Error: " . $e->getMessage());
    echo "Error al crear usuario y perfil";
}
```

---

## Mejores Prácticas

### 1. Usar Métodos Estáticos para Consultas Simples

```php
// ✓ Bueno
$user = User::where('email', 'john@example.com')->first();

// ✗ Innecesariamente complejo
$userModel = new User(true);
$user = $userModel->where('email', 'john@example.com')->first();
```

### 2. Usar Instancias para Consultas Complejas

```php
// ✓ Bueno
$userModel = new User(true);
$results = $userModel->join('profiles', 'users.id', 'profiles.user_id')
                     ->where('users.active', 1)
                     ->groupBy('users.role')
                     ->having('COUNT(*)', '>', 5)
                     ->get();

// ✗ No disponible como método estático
User::join(...)->groupBy(...)->having(...)->get();
```

### 3. Proteger contra Mass Assignment

```php
class User extends Model
{
    // Campos que NO se pueden asignar masivamente
    protected $not_fillable = ['id', 'is_admin', 'api_token'];
}
```

### 4. Ocultar Campos Sensibles

```php
class User extends Model
{
    // Estos campos no se incluirán en respuestas JSON
    protected $hidden = ['password', 'api_token', 'remember_token'];
}
```

### 5. Usar Hooks para Lógica Repetitiva

```php
protected function onCreating(Array &$data)
{
    // Hash password automáticamente
    if (isset($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    }
}
```

### 6. Validar Siempre los Datos

```php
function __construct(bool $connect = false)
{
    parent::__construct($connect);

    $this->schema['rules'] = [
        'email' => ['type' => 'email', 'required' => true],
        'age' => ['type' => 'int', 'min' => 18],
    ];

    $this->setValidator(new Validator());
}
```

### 7. Usar Transacciones para Operaciones Múltiples

```php
DB::beginTransaction();
try {
    // Operaciones...
    DB::commit();
} catch (Exception $e) {
    DB::rollback();
}
```

---

## Ejemplos Avanzados

### Ejemplo 1: Sistema de Autenticación

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\Validator;

class User extends Model
{
    protected static $table = 'users';

    protected $hidden = ['password', 'api_token'];
    protected $not_fillable = ['id', 'is_admin', 'created_at'];

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'users';

        $this->schema['rules'] = [
            'email' => ['type' => 'email', 'required' => true],
            'password' => ['type' => 'string', 'required' => true, 'min' => 8],
        ];

        $this->setValidator(new Validator());
    }

    protected function onCreating(Array &$data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
    }

    /**
     * Autenticar usuario
     */
    public static function authenticate($email, $password)
    {
        $user = static::where('email', $email)->first();

        if (!$user || empty($user)) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Generar API token
     */
    public static function generateToken($userId)
    {
        $token = bin2hex(random_bytes(32));

        $instance = new static(true);
        $instance->where('id', $userId)
                 ->update(['api_token' => $token]);

        return $token;
    }
}
```

**Uso:**

```php
// Registro
try {
    $userId = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123', // Se hasheará automáticamente
    ]);

    echo "Usuario registrado con ID: $userId";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Login
$user = User::authenticate('john@example.com', 'secret123');

if ($user) {
    $token = User::generateToken($user['id']);
    echo "Token: $token";
} else {
    echo "Credenciales inválidas";
}
```

### Ejemplo 2: Sistema de Blog con Relaciones

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

class Post extends Model
{
    protected static $table = 'posts';

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'posts';
    }

    /**
     * Obtener posts con información del autor
     */
    public static function withAuthor()
    {
        $instance = new static(true);
        return $instance->join('users', 'posts.user_id', 'users.id')
                       ->select([
                           'posts.*',
                           'users.name as author_name',
                           'users.email as author_email'
                       ])
                       ->get();
    }

    /**
     * Obtener posts con conteo de comentarios
     */
    public static function withCommentCount()
    {
        $instance = new static(true);
        return $instance->leftJoin('comments', 'posts.id', 'comments.post_id')
                       ->select([
                           'posts.*',
                           'COUNT(comments.id) as comment_count'
                       ])
                       ->groupBy('posts.id')
                       ->get();
    }

    /**
     * Buscar posts por título o contenido
     */
    public static function search($query)
    {
        $instance = new static(true);
        return $instance->where('title', 'LIKE', "%$query%")
                       ->orWhere('content', 'LIKE', "%$query%")
                       ->orderBy('created_at', 'DESC')
                       ->get();
    }
}
```

**Uso:**

```php
// Obtener posts con autores
$posts = Post::withAuthor();

foreach ($posts as $post) {
    echo $post['title'] . " por " . $post['author_name'] . "\n";
}

// Obtener posts con conteo de comentarios
$postsWithComments = Post::withCommentCount();

foreach ($postsWithComments as $post) {
    echo $post['title'] . " - " . $post['comment_count'] . " comentarios\n";
}

// Buscar posts
$results = Post::search('Laravel');
```

### Ejemplo 3: Soft Deletes y Auditoría

```php
<?php

namespace Boctulus\Simplerest\Models;

use Boctulus\Simplerest\Core\Model;

class Product extends Model
{
    protected static $table = 'products';

    function __construct(bool $connect = false)
    {
        parent::__construct($connect);
        $this->table_name = 'products';
    }

    protected function onDeleting(Array &$data)
    {
        // Registrar en tabla de auditoría
        $auditModel = new Audit(true);
        $auditModel->create([
            'table' => 'products',
            'action' => 'delete',
            'record_id' => $data['id'] ?? null,
            'old_data' => json_encode($data),
            'user_id' => $_SESSION['user_id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Obtener productos eliminados
     */
    public static function onlyTrashed()
    {
        $instance = new static(true);
        return $instance->whereNotNull('deleted_at')->get();
    }

    /**
     * Restaurar producto
     */
    public static function restoreById($id)
    {
        $instance = new static(true);
        return $instance->where('id', $id)
                       ->update(['deleted_at' => null]);
    }
}
```

---

## Resumen

El ORM de SimpleRest ofrece:

✓ **Dos estilos de trabajo**: Tradicional e Laravel-like
✓ **Query Builder potente**: Joins, subconsultas, agregaciones
✓ **Hooks y eventos**: Para lógica personalizada
✓ **Mutadores**: Transformación automática de datos
✓ **Validación integrada**: Validar antes de guardar
✓ **Multi-tenant**: Múltiples conexiones de BD
✓ **Performance**: Caching interno y optimizaciones
✓ **Compatibilidad Laravel**: Sintaxis familiar

---

## Ver También

- [ORM-Laravel-like.md](ORM-Laravel-like.md) - Métodos estáticos en detalle
- [QueryBuilder.md](QueryBuilder.md) - Documentación completa del Query Builder
- [Exceptions.md](Exceptions.md) - Manejo de excepciones

---

## Autor

**Pablo Bozzolo (boctulus)**
Software Architect

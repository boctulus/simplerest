# ORM - Laravel-like Active Record Pattern

SimpleRest implementa métodos ORM estáticos al estilo Laravel (Active Record pattern) que permiten trabajar con modelos de forma más intuitiva y expresiva.

## Tabla de Contenidos

- [Introducción](#introducción)
- [Configuración de Modelos](#configuración-de-modelos)
  - [Generar Modelos](#generar-modelos)
  - [Renombrar Modelos](#renombrar-modelos)
  - [Configurar Propiedad $table](#configurar-propiedad-table)
  - [Configurar Conexión de Base de Datos](#configurar-conexión-de-base-de-datos)
- [Métodos Estáticos Disponibles](#métodos-estáticos-disponibles)
  - [where()](#where)
  - [create()](#create)
  - [findOrFail()](#findorfail)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Diferencias con Laravel](#diferencias-con-laravel)
- [Notas Importantes](#notas-importantes)

---

## Introducción

Los métodos ORM estáticos permiten realizar consultas y operaciones sobre la base de datos de forma más limpia y legible:

```php
// Estilo tradicional
$instance = new Brand(true);
$brand = $instance->where('brand', 'ARIEL')->first();

// Estilo Laravel-like (ORM estático)
$brand = Brand::where('brand', 'ARIEL')->first();
```

---

## Configuración de Modelos

Para usar los métodos ORM estáticos, los modelos deben estar correctamente configurados.

### Generar Modelos

**Opción 1: Usar el comando make (recomendado)**

Si existe un comando para generar modelos, úsalo:

```bash
php com make model BrandModel --table=brands --connection=zippy
```

**Opción 2: Crear manualmente**

Crea el archivo del modelo en la ubicación apropiada:
- Para packages: `packages/{vendor}/{package}/src/Models/`
- Para la app principal: `app/Models/`

```php
<?php

namespace Boctulus\Zippy\Models;

use Boctulus\Simplerest\Core\Model as MyModel;

class Brand extends MyModel
{
    protected $hidden       = [];
    protected $not_fillable = [];
    protected $field_names  = [];
    protected $formatters   = [];

    function __construct(bool $connect = false){
        parent::__construct($connect);
    }
}
```

### Renombrar Modelos

⚠️ **IMPORTANTE**: Los archivos de modelo deben cumplir con PSR-4.

**Problema común:**
```
✗ BrandModel.php     // No cumple PSR-4
✓ Brand.php          // Correcto
```

Si el generador crea archivos con sufijo "Model", debes renombrarlos:

```bash
# Windows
cd packages\boctulus\zippy\src\Models
move BrandModel.php Brand.php
move BrandCategoryModel.php BrandCategory.php

# Linux/Mac
cd packages/boctulus/zippy/src/Models
mv BrandModel.php Brand.php
mv BrandCategoryModel.php BrandCategory.php
```

**Regenerar autoload después del renombrado:**

```bash
composer dumpautoload
```

El autoloader te advertirá si los nombres no cumplen con PSR-4:

```
⚠ Class Boctulus\Zippy\Models\Brand located in ./packages/boctulus/zippy/src/Models/BrandModel.php
  does not comply with psr-4 autoloading standard
```

### Configurar Propiedad $table

Agrega la propiedad estática `$table` que especifica el nombre de la tabla en la base de datos:

```php
<?php

namespace Boctulus\Zippy\Models;

use Boctulus\Simplerest\Core\Model as MyModel;

class Brand extends MyModel
{
    // ⚠️ OBLIGATORIO: Nombre de la tabla
    protected static $table = 'brands';

    protected $hidden       = [];
    protected $not_fillable = [];
    protected $field_names  = [];
    protected $formatters   = [];

    function __construct(bool $connect = false){
        parent::__construct($connect);

        // Establecer nombre de tabla en la instancia
        $this->table_name = 'brands';
    }
}
```

### Configurar Conexión de Base de Datos

Si tu modelo usa una conexión diferente a la principal (ej: base de datos de un package), configúrala en el constructor:

```php
function __construct(bool $connect = false){
    parent::__construct($connect);

    // Nombre de la tabla
    $this->table_name = 'brands';

    // ⚠️ IMPORTANTE: Conexión específica
    $this->setConn(\Boctulus\Simplerest\Core\Libs\DB::getConnection('zippy'));
}
```

**Ejemplo completo de modelo configurado:**

```php
<?php

namespace Boctulus\Zippy\Models;

use Boctulus\Simplerest\Core\Model as MyModel;

class BrandCategory extends MyModel
{
    protected static $table = 'brand_categories';

    protected $hidden       = [];
    protected $not_fillable = [];
    protected $field_names  = [];
    protected $formatters   = [];

    function __construct(bool $connect = false){
        parent::__construct($connect);

        // Tabla
        $this->table_name = 'brand_categories';

        // Conexión específica (si aplica)
        $this->setConn(\Boctulus\Simplerest\Core\Libs\DB::getConnection('zippy'));
    }
}
```

---

## Métodos Estáticos Disponibles

### where()

Aplica una cláusula WHERE a la consulta.

**Firma:**
```php
static function where(...$args): static
```

**Sintaxis:**

```php
// Sintaxis 1: Campo y valor
Model::where('field', 'value')

// Sintaxis 2: Campo, operador y valor
Model::where('field', '>', 'value')

// Sintaxis 3: Array de condiciones
Model::where([
    ['field1', 'value1'],
    ['field2', '>', 'value2']
])

// Sintaxis 4: Callable (grupos)
Model::where(function($query) {
    $query->where('field1', 'value1')
          ->orWhere('field2', 'value2');
})
```

**Ejemplos:**

```php
use Boctulus\Zippy\Models\Brand;

// Buscar por nombre exacto
$brand = Brand::where('brand', 'ARIEL')->first();

// Buscar con operador
$brands = Brand::where('id', '>', 100)->get();

// Múltiples condiciones
$brand = Brand::where([
    ['brand', 'ARIEL'],
    ['deleted_at', null]
])->first();
```

**Métodos encadenables después de where():**

```php
Brand::where('brand', 'ARIEL')
    ->first();           // Obtener primer resultado (array)

Brand::where('id', '>', 10)
    ->get();             // Obtener todos los resultados

Brand::where('brand', 'ARIEL')
    ->exists();          // Verificar si existe

Brand::where('deleted_at', null)
    ->count();           // Contar registros

Brand::where('brand', 'LIKE', '%ARIEL%')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

### create()

Crea un nuevo registro en la base de datos.

**Firma:**
```php
static function create(array $data, $ignore_duplicates = false): mixed
```

**Parámetros:**
- `$data`: Array asociativo con los campos y valores
- `$ignore_duplicates`: Si es `true`, ignora errores de duplicados

**Retorna:**
- ID del registro insertado (si es exitoso)
- `false` si falla

**Ejemplo:**

```php
use Boctulus\Zippy\Models\BrandCategory;

// Crear un registro simple
$id = BrandCategory::create([
    'brand_id' => 123,
    'category_id' => 'CtYqb4eWfeZjXszLsdI3',
    'confidence_level' => 'high',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);

echo "Registro creado con ID: $id";

// Crear ignorando duplicados
$id = BrandCategory::create([
    'brand_id' => 123,
    'category_id' => 'CtYqb4eWfeZjXszLsdI3',
    'confidence_level' => 'high',
], true);
```

**Inserción múltiple:**

```php
// Array de arrays para insertar múltiples registros
BrandCategory::create([
    [
        'brand_id' => 1,
        'category_id' => 'cat1',
        'confidence_level' => 'high',
    ],
    [
        'brand_id' => 2,
        'category_id' => 'cat2',
        'confidence_level' => 'medium',
    ],
]);
```

### findOrFail()

Busca un registro por su ID y lanza una excepción si no existe.

**Firma:**
```php
static function findOrFail($id): static
```

**Parámetros:**
- `$id`: ID del registro a buscar

**Retorna:**
- Instancia del modelo con los datos cargados

**Lanza:**
- `Exception` si el registro no existe

**Ejemplo:**

```php
use Boctulus\Zippy\Models\Brand;

try {
    $brand = Brand::findOrFail(123);

    // Acceder a propiedades usando magic methods
    echo $brand->brand;
    echo $brand->id;

} catch (Exception $e) {
    echo "Brand no encontrado: " . $e->getMessage();
}
```

---

## Ejemplos de Uso

### Ejemplo 1: Script de Categorización de Marcas

```php
<?php

use Boctulus\Zippy\Models\Brand;
use Boctulus\Zippy\Models\BrandCategory;

require_once __DIR__ . '/../../app.php';

$mappings = [
    'ARIEL' => 'UjMgQMJo1zW3H4Ui68ll',    // Limpieza
    'ARLISTAN' => 'QJ4IL7yrSzPSdNpidQTh', // Infusiones
    'ADES' => 'CtYqb4eWfeZjXszLsdI3',     // Bebidas
    'ABSOLUT' => 'CtYqb4eWfeZjXszLsdI3',  // Bebidas
];

$count = 0;
foreach ($mappings as $brandName => $categoryId) {
    // Buscar marca por nombre
    $brand = Brand::where('brand', $brandName)->first();

    if (!$brand || empty($brand)) {
        echo "Brand not found: $brandName\n";
        continue;
    }

    // Verificar si ya existe el mapping
    $exists = BrandCategory::where('brand_id', $brand['id'])->exists();

    if ($exists) {
        echo "Mapping already exists for: $brandName\n";
        continue;
    }

    // Crear el mapping
    BrandCategory::create([
        'brand_id' => $brand['id'],
        'category_id' => $categoryId,
        'confidence_level' => 'high',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);

    echo "Mapped: $brandName -> $categoryId\n";
    $count++;
}

echo "Done. Mapped $count brands.\n";
```

### Ejemplo 2: CRUD Completo

```php
<?php

use Boctulus\Zippy\Models\Brand;

// CREATE
$id = Brand::create([
    'brand' => 'COCA COLA',
    'normalized_brand' => 'coca-cola',
    'created_at' => date('Y-m-d H:i:s'),
]);

// READ
$brand = Brand::findOrFail($id);

// READ con WHERE
$brands = Brand::where('normalized_brand', 'LIKE', 'coca%')->get();

// UPDATE (usando instancia)
$brand = Brand::findOrFail($id);
// Modificar usando métodos del query builder tradicional
$instance = new Brand(true);
$instance->where('id', $id)
         ->update(['brand' => 'COCA-COLA']);

// DELETE (soft delete si está configurado)
$instance = new Brand(true);
$instance->where('id', $id)->delete();
```

### Ejemplo 3: Consultas Complejas

```php
<?php

use Boctulus\Zippy\Models\Brand;
use Boctulus\Zippy\Models\BrandCategory;

// Buscar marcas sin categoría
$brandsWithoutCategory = Brand::where(function($query) {
    // Subconsulta compleja requiere usar instancia
    // Los métodos estáticos son ideales para consultas simples
})->get();

// Contar categorías por nivel de confianza
$highConfidenceCount = BrandCategory::where('confidence_level', 'high')
                                    ->count();

// Buscar y verificar existencia
$exists = BrandCategory::where([
    ['brand_id', 123],
    ['category_id', 'xyz']
])->exists();

if (!$exists) {
    BrandCategory::create([
        'brand_id' => 123,
        'category_id' => 'xyz',
        'confidence_level' => 'medium',
    ]);
}
```

---

## Diferencias con Laravel

### 1. Resultados como Arrays

En SimpleRest, `first()` y `get()` devuelven **arrays**, no objetos:

```php
// Laravel
$brand = Brand::where('brand', 'ARIEL')->first();
echo $brand->id;  // Acceso a propiedad

// SimpleRest
$brand = Brand::where('brand', 'ARIEL')->first();
echo $brand['id'];  // Acceso a array ✓
```

### 2. findOrFail() devuelve instancia hidratada

```php
// findOrFail() sí devuelve un objeto
$brand = Brand::findOrFail(123);
echo $brand->id;  // ✓ Funciona con magic methods
```

### 3. Métodos limitados

No todos los métodos estáticos de Laravel están implementados. Actualmente disponibles:
- `where()`
- `create()`
- `findOrFail()`

Para operaciones más complejas, usa la instancia tradicional:

```php
// Operación compleja - usar instancia
$instance = new Brand(true);
$result = $instance->where('brand', 'ARIEL')
                   ->with('categories')
                   ->groupBy('normalized_brand')
                   ->having('count', '>', 5)
                   ->get();
```

### 4. Relaciones Automáticas con Schemas

SimpleRest **SÍ tiene relaciones automáticas** cuando usas schemas. Sin embargo, con métodos estáticos el acceso es limitado.

**Con Schemas (automático):**

```php
// Schema define la relación
class BrandSchema implements ISchema
{
    static function get(){
        return [
            // ...
            'relationships' => [
                'brand_categories' => [
                    ['brands.id', 'brand_categories.brand_id']
                ]
            ],
        ];
    }
}

// Modelo con schema
class Brand extends MyModel
{
    function __construct(bool $connect = false){
        parent::__construct($connect, BrandSchema::class);  // ⚠️ Pasar schema
        $this->table_name = 'brands';
    }
}

// Usar relaciones (requiere instancia)
$brandModel = new Brand(true);
$brands = $brandModel->connectTo('brand_categories')->get();

// Resultado incluye brand_categories automáticamente
```

**Sin Schemas (manual):**

```php
// SimpleRest - usar joins tradicionales
$instance = new Brand(true);
$result = $instance->join('brand_categories', 'brands.id', 'brand_categories.brand_id')
                   ->where('brands.id', 123)
                   ->get();

// O crear métodos helper
class Brand extends MyModel
{
    public function categories()
    {
        if (!isset($this->orm_attributes['id'])) {
            return [];
        }

        return BrandCategory::where('brand_id', $this->orm_attributes['id'])->get();
    }
}
```

**Nota importante:** Las relaciones automáticas con `connectTo()` requieren usar **instancias**, no métodos estáticos. Para más información sobre relaciones, ver [ORM.md](ORM.md#relaciones).

---

## Notas Importantes

### ⚠️ Cumplimiento de PSR-4

Los nombres de archivo **deben coincidir exactamente** con los nombres de clase:

```php
// Archivo: Brand.php
class Brand extends MyModel { }  // ✓

// Archivo: BrandModel.php
class Brand extends MyModel { }  // ✗ No cumple PSR-4
```

### ⚠️ Regenerar Autoload

Siempre que renombres archivos o crees nuevos modelos:

```bash
composer dumpautoload
```

### ⚠️ Propiedad $table es Obligatoria

Sin `protected static $table`, los métodos estáticos no funcionarán:

```php
class Brand extends MyModel
{
    protected static $table = 'brands';  // ⚠️ OBLIGATORIO
}
```

### ⚠️ Configurar Conexión

Si el modelo usa una base de datos diferente a la principal:

```php
function __construct(bool $connect = false){
    parent::__construct($connect);
    $this->table_name = 'brands';

    // ⚠️ Especificar conexión
    $this->setConn(\Boctulus\Simplerest\Core\Libs\DB::getConnection('zippy'));
}
```

### ⚠️ Arrays vs Objetos

Recuerda que `first()` y `get()` devuelven arrays:

```php
$brand = Brand::where('id', 123)->first();

// ✗ Incorrecto
echo $brand->id;

// ✓ Correcto
echo $brand['id'];

// ✓ También correcto con findOrFail()
$brand = Brand::findOrFail(123);
echo $brand->id;  // Magic method __get()
```

### ✓ Cuándo Usar Métodos Estáticos

**Usa métodos estáticos para:**
- Consultas simples (where, find, create)
- Scripts de mantenimiento
- Código más legible y expresivo

**Usa instancias tradicionales para:**
- Consultas complejas con joins
- Subconsultas
- Operaciones que requieren múltiples métodos encadenados
- Cuando necesites acceso completo al Query Builder

---

## Checklist de Configuración

Al crear un nuevo modelo para usar métodos ORM estáticos:

- [ ] Generar modelo (manual o con comando)
- [ ] Renombrar archivo sin sufijo "Model" (ej: `Brand.php`, no `BrandModel.php`)
- [ ] Agregar `protected static $table = 'nombre_tabla';`
- [ ] Configurar `$this->table_name` en el constructor
- [ ] Configurar conexión con `setConn()` si es necesario
- [ ] Ejecutar `composer dumpautoload`
- [ ] Verificar que no hay warnings de PSR-4
- [ ] Probar con una consulta simple: `Model::where('id', 1)->first()`

---

## Autor

**Pablo Bozzolo (boctulus)**
Software Architect

---

## Relacionado

- [Query Builder](QueryBuilder.md) - Documentación completa del Query Builder
- [Command Line](CommandLine.md) - Comandos disponibles para generar modelos
- [Packages & Modules](Packages%20and%20Modules.md) - Estructura de packages

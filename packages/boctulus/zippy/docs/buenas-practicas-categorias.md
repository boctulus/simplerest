# Guía de Buenas Prácticas para Categorías - Zippy

**Autor:** Pablo Bozzolo (boctulus)
**Fecha:** 2025-12-07
**Versión:** 1.0

---

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Principios Fundamentales](#principios-fundamentales)
3. [Formato de Slugs](#formato-de-slugs)
4. [Formato de Nombres](#formato-de-nombres)
5. [Jerarquía de Categorías](#jerarquía-de-categorías)
6. [Errores Comunes a Evitar](#errores-comunes-a-evitar)
7. [Uso de CategoryMapper](#uso-de-categorymapper)
8. [Validación y Testing](#validación-y-testing)
9. [Mantenimiento](#mantenimiento)

---

## Introducción

Este documento establece las directrices para la creación, normalización y mantenimiento de categorías en el sistema Zippy. Seguir estas prácticas garantiza consistencia, evita duplicados y facilita el mantenimiento del catálogo de productos.

---

## Principios Fundamentales

### 1. **Consistencia Absoluta**
Todas las categorías deben seguir el mismo formato y convenciones en todo el sistema.

### 2. **Idioma Único: Español**
- **SIEMPRE** usar español para nombres y slugs
- **NUNCA** mezclar inglés y español
- **NUNCA** usar términos técnicos en inglés a menos que sean nombres propios

### 3. **Normalización Obligatoria**
- **SIEMPRE** usar `Strings::slug()` para generar slugs
- **NUNCA** crear slugs manualmente
- **NUNCA** asumir que un string ya está normalizado

### 4. **Evitar Duplicados**
- Verificar si la categoría ya existe antes de crear una nueva
- Usar mappings (aliases) para variaciones del mismo concepto

---

## Formato de Slugs

### ✅ Reglas para Slugs

1. **SIEMPRE usar `Strings::slug()` para generarlos**
   ```php
   // ✅ CORRECTO
   $slug = Strings::slug($nombre);

   // ❌ INCORRECTO - No crear slugs manualmente
   $slug = strtolower(str_replace(' ', '-', $nombre));
   ```

2. **Formato válido:**
   - Solo minúsculas
   - Solo caracteres alfanuméricos (a-z, 0-9)
   - Guiones (-) para separar palabras
   - Sin acentos ni caracteres especiales
   - Sin espacios
   - Sin guiones duplicados
   - Sin guiones al inicio o final

3. **Ejemplos:**
   ```php
   // ✅ CORRECTO
   'frutas-y-verduras'
   'comida-gourmet'
   'productos-frescos-y-lacteos'
   'snacks-y-golosinas-premium'

   // ❌ INCORRECTO
   'Frutas-y-Verduras'           // Mayúsculas
   'frutas y verduras'           // Espacios
   'frutas_y_verduras'           // Guión bajo
   'frutas-y-verduras-'          // Guión al final
   'frutas--y--verduras'         // Guiones duplicados
   'fresh-produce'               // Inglés
   'gourmetfood'                 // Sin guiones
   'gourmetfoodcategory'         // Sufijo innecesario
   ```

### 🚫 Errores Críticos de Slugs

#### Error 1: Slugs con Espacios
```php
// ❌ MAL
$slug = 'fresh produce and dairy category';

// ✅ BIEN
$slug = Strings::slug('Productos Frescos y Lácteos'); // 'productos-frescos-y-lacteos'
```

#### Error 2: Slugs en Inglés
```php
// ❌ MAL
$slug = 'gourmet-food';

// ✅ BIEN
$slug = Strings::slug('Comida Gourmet'); // 'comida-gourmet'
```

#### Error 3: Sufijos Innecesarios
```php
// ❌ MAL
$slug = 'comida-gourmet-category';
$slug = 'frutas-y-verduras-cat';

// ✅ BIEN
$slug = Strings::slug('Comida Gourmet');        // 'comida-gourmet'
$slug = Strings::slug('Frutas y Verduras');     // 'frutas-y-verduras'
```

---

## Formato de Nombres

### ✅ Reglas para Nombres

1. **Idioma:** Siempre en español
2. **Capitalización:** Tipo título (Primera Letra De Cada Palabra)
3. **Sin sufijos innecesarios:** No agregar "Category", "Categoría", etc.
4. **Claridad:** Nombres descriptivos y específicos

### Ejemplos:

```php
// ✅ CORRECTO
'Frutas y Verduras'
'Comida Gourmet'
'Productos Frescos y Lácteos'
'Snacks y Golosinas Premium'
'Bebidas'

// ❌ INCORRECTO
'frutas y verduras'              // Sin capitalizar
'FRUTAS Y VERDURAS'              // Todo mayúsculas
'Frutas Y Verduras'              // 'Y' capitalizada incorrectamente
'Fresh Produce'                  // Inglés
'GourmetFood'                    // CamelCase
'Comida Gourmet Category'        // Sufijo innecesario
'Categoría de Comida Gourmet'    // Redundante
```

### Palabras Conectoras (No Capitalizar)

En español, las siguientes palabras NO se capitalizan en nombres tipo título:
- `y`, `e`, `o`, `u`
- `de`, `del`, `la`, `el`, `los`, `las`
- `a`, `con`, `sin`, `por`, `para`

```php
// ✅ CORRECTO
'Frutas y Verduras'
'Productos Frescos y Lácteos'
'Aceites y Condimentos'
'Snacks y Golosinas Premium'

// ❌ INCORRECTO
'Frutas Y Verduras'
'Productos Frescos Y Lácteos'
```

---

## Jerarquía de Categorías

### Estructura Recomendada

```
Raíz
├── Frescos
│   ├── Frutas y Verduras
│   ├── Carnes
│   ├── Embutidos
│   ├── Lácteos
│   └── Productos Frescos y Lácteos
├── Bebidas
│   ├── Aperitivos
│   └── Infusiones
├── Golosinas
│   ├── Alfajores
│   ├── Bombones
│   └── Snacks y Golosinas Premium
└── Hogar y Bazar
    ├── Electro
    └── Limpieza
```

### Reglas de Jerarquía

1. **Máximo 3 niveles** de profundidad recomendado
2. **parent_slug** debe referenciar un slug válido existente
3. **Verificar integridad** de relaciones padre-hijo
4. **Evitar ciclos** (una categoría no puede ser padre de sí misma)

```php
// ✅ CORRECTO
DB::table('categories', 'zippy')->insert([
    'id' => uniqid('cat_'),
    'name' => 'Frutas y Verduras',
    'slug' => Strings::slug('Frutas y Verduras'),
    'parent_slug' => 'frescos', // Existe previamente
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);

// ❌ INCORRECTO
DB::table('categories', 'zippy')->insert([
    'name' => 'Frutas y Verduras',
    'slug' => 'frutas-y-verduras',  // No usa Strings::slug()
    'parent_slug' => 'frescos-category', // Slug incorrecto
]);
```

---

## Errores Comunes a Evitar

### ❌ Error 1: No Usar `Strings::slug()`

```php
// ❌ MAL - Normalización manual
$slug = strtolower(str_replace(' ', '-', $name));

// ❌ MAL - Usar slug directamente del input
$slug = $input['slug'];

// ✅ BIEN - Siempre usar Strings::slug()
$slug = Strings::slug($name);
```

### ❌ Error 2: Crear Duplicados

```php
// ❌ MAL - No verificar si existe
DB::table('categories', 'zippy')->insert([
    'name' => 'Comida Gourmet',
    'slug' => Strings::slug('Comida Gourmet'),
]);

// ✅ BIEN - Verificar primero
$slug = Strings::slug('Comida Gourmet');
$exists = table('categories')
    ->where(['slug' => $slug])
    ->whereNull('deleted_at')
    ->first();

if (!$exists) {
    table('categories')->insert([
        'id' => uniqid('cat_'),
        'name' => 'Comida Gourmet',
        'slug' => $slug,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
}
```

### ❌ Error 3: Mezclar Idiomas

```php
// ❌ MAL
$categories = [
    ['name' => 'Gourmet Food', 'slug' => 'gourmet-food'],
    ['name' => 'Fresh Produce', 'slug' => 'fresh-produce'],
];

// ✅ BIEN
$categories = [
    ['name' => 'Comida Gourmet', 'slug' => Strings::slug('Comida Gourmet')],
    ['name' => 'Productos Frescos', 'slug' => Strings::slug('Productos Frescos')],
];
```

### ❌ Error 4: Agregar Sufijos Innecesarios

```php
// ❌ MAL
$name = 'Comida Gourmet Category';
$name = 'GourmetFoodCategory';
$slug = 'comida-gourmet-cat';

// ✅ BIEN
$name = 'Comida Gourmet';
$slug = Strings::slug($name); // 'comida-gourmet'
```

### ❌ Error 5: No Normalizar Encoding UTF-8

```php
// ❌ MAL - Encoding corrupto
$name = 'Productos Frescos y Lácteos'; // Mal codificado

// ✅ BIEN - Usar Strings::fixUTF8() si es necesario
$name = Strings::fixUTF8('Productos Frescos y Lácteos');
$slug = Strings::slug($name);
```

---

## Uso de CategoryMapper

### Creación de Categorías

**SIEMPRE** usar `CategoryMapper::resolve()` para crear/encontrar categorías:

```php
use Boctulus\Zippy\Libs\CategoryMapper;

// ✅ CORRECTO - El mapper normaliza automáticamente
$result = CategoryMapper::resolve('Comida Gourmet');

if ($result['created']) {
    echo "Nueva categoría creada: {$result['category_slug']}";
} else {
    echo "Categoría encontrada: {$result['category_slug']}";
}

// El slug ya está normalizado correctamente
// No necesitas llamar a Strings::slug() manualmente
```

### Resolución de Productos

```php
// ✅ CORRECTO
$product = [
    'catego_raw1' => 'Aceites Y Condimentos',
    'catego_raw2' => 'Condimentos',
    'description' => 'Aceite de oliva extra virgen'
];

$categories = CategoryMapper::resolveProduct($product);
// Retorna: ['almacen', 'aceites-y-condimentos']
```

### Creación Manual (Solo cuando sea absolutamente necesario)

```php
// Si DEBES crear manualmente, seguir este patrón:

use Boctulus\Simplerest\Core\Libs\Strings;

$nombre = 'Frutas y Verduras';
$slug = Strings::slug($nombre); // ⚠️ CRÍTICO: Siempre usar Strings::slug()

// Verificar que no existe
$exists = table('categories')
    ->where(['slug' => $slug])
    ->whereNull('deleted_at')
    ->first();

if ($exists) {
    throw new \Exception("La categoría '$slug' ya existe");
}

// Crear
table('categories')->insert([
    'id' => uniqid('cat_'),
    'name' => $nombre,
    'slug' => $slug,
    'parent_slug' => 'frescos', // Opcional
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);
```

---

## Validación y Testing

### Checklist de Validación Pre-Deploy

Antes de crear/actualizar categorías, verificar:

- [ ] ¿El slug fue generado con `Strings::slug()`?
- [ ] ¿El nombre está en español?
- [ ] ¿El nombre usa capitalización tipo título correcta?
- [ ] ¿No tiene sufijos innecesarios ("Category", "Categoría")?
- [ ] ¿No existen duplicados con el mismo concepto?
- [ ] ¿El parent_slug (si existe) referencia una categoría válida?
- [ ] ¿El encoding UTF-8 es correcto?

### Script de Validación

```php
// Ejecutar para validar categorías existentes
php com zippy category all

// Ver estructura jerárquica
php com zippy category tree

// Verificar integridad
php com zippy category report_issues

// Listar categorías raw para mapear
php com zippy category list_raw --limit=100
```

### Tests Unitarios

```php
use Boctulus\Simplerest\Core\Libs\Strings;

// Test de normalización de slugs
public function testSlugNormalization()
{
    $tests = [
        'Frutas y Verduras' => 'frutas-y-verduras',
        'Comida Gourmet' => 'comida-gourmet',
        'Fresh Produce' => 'fresh-produce',  // Aunque sea inglés, normaliza
        'Productos Frescos Y Lácteos' => 'productos-frescos-y-lacteos',
    ];

    foreach ($tests as $input => $expected) {
        $this->assertEquals($expected, Strings::slug($input));
    }
}
```

---

## Mantenimiento

### Auditoría Periódica

Ejecutar mensualmente para detectar problemas:

```bash
# 1. Detectar slugs con espacios
php com sql select "SELECT * FROM categories WHERE slug LIKE '% %'" --connection=zippy

# 2. Detectar nombres en inglés (heurística simple)
php com sql select "SELECT * FROM categories WHERE name REGEXP 'Category|Food|Fresh|Premium|Snacks'" --connection=zippy

# 3. Detectar categorías huérfanas
php com zippy category find_orphans

# 4. Detectar padres faltantes
php com zippy category find_missing_parents
```

### Consolidación de Duplicados

Si se detectan duplicados, usar el método `CategoryMapper::mergeCategories()`:

```php
use Boctulus\Zippy\Libs\CategoryMapper;

$translations = [
    // slug_actual => [nuevo_slug, nuevo_nombre]
    'gourmet food' => ['comida-gourmet', 'Comida Gourmet'],
    'gourmetfood' => ['comida-gourmet', 'Comida Gourmet'],
    'gourmetfoodcategory' => ['comida-gourmet', 'Comida Gourmet'],
];

CategoryMapper::mergeCategories($translations, $verbose = true);
```

### Limpieza de Categorías Eliminadas

```bash
# Eliminar físicamente categorías con deleted_at
php scripts/tmp/cleanup_deleted_categories.php --dry-run  # Primero simular
php scripts/tmp/cleanup_deleted_categories.php           # Luego ejecutar
```

---

## Resumen de Comandos Útiles

```bash
# Listar todas las categorías
php com zippy category all

# Ver árbol jerárquico
php com zippy category tree

# Listar categorías raw detectadas
php com zippy category list_raw --limit=100

# Probar mapeo de una categoría raw
php com zippy category test --raw="Aceites Y Condimentos"

# Resolver categoría con LLM
php com zippy category resolve --text="Leche entera 1L"

# Crear categoría
php com zippy category create --name="Frutas Secas" --parent=almacen

# Establecer padre de una categoría
php com zippy category set --slug=frutas-secas --parent=almacen

# Reportar problemas de integridad
php com zippy category report_issues

# Generar comandos para crear categorías faltantes
php com zippy category generate_create_commands
```

---

## Referencias

- **Documentación Strings:** `docs/core-libs/Strings.md`
- **Documentación CategoryMapper:** `docs/packages/zippy/CategoryMapper.md`
- **Comandos Zippy:** `docs/CommandLine.md`
- **Changelog de Categorías:** `docs/CHANGELOG-categorias.md`

---

## Cambios Históricos

### 2025-12-07 - v1.0
- ✅ Consolidadas 11 categorías duplicadas
- ✅ Normalizados todos los slugs usando `Strings::slug()`
- ✅ Eliminadas 5 categorías con deleted_at
- ✅ Corregido CategoryMapper para usar siempre `Strings::slug()`
- ✅ Traducidas todas las categorías de inglés a español
- ✅ Removidos sufijos innecesarios ("Category")

---

**Autor:** Pablo Bozzolo (boctulus)
**Software Architect**
**Última actualización:** 2025-12-07

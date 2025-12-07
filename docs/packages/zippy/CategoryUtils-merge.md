# CategoryUtils::merge() - Fusión de Categorías Duplicadas

**Autor:** Pablo Bozzolo (boctulus)
**Fecha:** 2025-12-07
**Versión:** 1.0

---

## 📋 Descripción

El método `CategoryUtils::merge()` permite fusionar múltiples categorías duplicadas en una sola categoría definitiva, actualizando automáticamente todas las referencias en el sistema.

**Ubicación:** `packages/boctulus/zippy/src/Libs/CategoryUtils.php`

---

## 🎯 Propósito

Cuando se detectan categorías duplicadas (mismo concepto, nombres similares), este método permite consolidarlas manteniendo la integridad referencial en todas las tablas del sistema.

### Ejemplo de Uso Real

Si tienes estas categorías duplicadas:
- `cat_001`: "Frutas y Verduras" (slug: `frutas-y-verduras`) ← Definitiva
- `cat_002`: "Frutas Y Verduras" (slug: `frutas-y-verduras-2`)
- `cat_003`: "FrutasYVerduras" (slug: `frutasyverduras`)

Puedes fusionarlas usando:
```php
CategoryUtils::merge('cat_001', 'cat_002', 'cat_003');
```

---

## 📝 Sintaxis

```php
public static function merge(string ...$category_ids): array
```

### Parámetros

- **`...$category_ids`** _(string, variadic)_ - IDs de las categorías a fusionar
  - **Primer parámetro**: ID de la categoría que se mantendrá (definitiva)
  - **Resto de parámetros**: IDs de las categorías que se eliminarán
  - **Mínimo**: 2 categorías requeridas

### Retorno

Retorna un array asociativo con estadísticas de la fusión:

```php
[
    'target_category' => 'Nombre (ID: cat_xxx, slug: xxx)',
    'merged_categories' => [
        'Nombre 1 (ID: cat_yyy, slug: yyy)',
        'Nombre 2 (ID: cat_zzz, slug: zzz)',
    ],
    'category_mappings_updated' => 15,
    'brand_categories_updated' => 3,
    'products_updated' => 42,
    'categories_deleted' => 2,
]
```

### Excepciones

Lanza `\Exception` en los siguientes casos:
- Menos de 2 categorías proporcionadas
- La categoría destino no existe o está eliminada
- Una o más categorías origen no existen o están eliminadas

---

## ⚙️ Proceso de Fusión

### 1. Actualizar `category_mappings`

Actualiza todos los mappings (aliases) que referencian las categorías origen:

```sql
-- Por category_slug
UPDATE category_mappings
SET category_slug = 'frutas-y-verduras', updated_at = NOW()
WHERE category_slug IN ('frutas-y-verduras-2', 'frutasyverduras')
  AND deleted_at IS NULL;

-- Por category_id
UPDATE category_mappings
SET category_id = 'cat_001', updated_at = NOW()
WHERE category_id IN ('cat_002', 'cat_003')
  AND deleted_at IS NULL;
```

### 2. Actualizar `brand_categories`

Actualiza las relaciones marca-categoría:

```sql
UPDATE brand_categories
SET category_id = 'cat_001', updated_at = NOW()
WHERE category_id IN ('cat_002', 'cat_003')
  AND deleted_at IS NULL;
```

### 3. Actualizar `products` (campo JSON)

Para cada producto que contiene los slugs a eliminar en su campo JSON `categories`:

```php
// Antes
{"categories": ["frutas-y-verduras-2", "almacen"]}

// Después
{"categories": ["frutas-y-verduras", "almacen"]}
```

El proceso:
1. Lee el campo JSON
2. Reemplaza slugs origen por slug destino
3. Elimina duplicados (si el slug destino ya existía)
4. Actualiza el producto solo si hubo cambios

### 4. Soft Delete de Categorías Origen

Marca las categorías origen como eliminadas:

```sql
UPDATE categories
SET deleted_at = NOW()
WHERE id IN ('cat_002', 'cat_003');
```

---

## 💻 Ejemplos de Uso

### Ejemplo 1: Uso Directo en PHP

```php
use Boctulus\Zippy\Libs\CategoryUtils;

// Fusionar 3 categorías duplicadas
$stats = CategoryUtils::merge(
    'cat_675ca59cb8ad9',  // Definitiva
    'cat_675ca5a1b02f0',  // Se eliminará
    'cat_675ca5a8ac9a8'   // Se eliminará
);

print_r($stats);
```

**Output:**
```
Array
(
    [target_category] => Frutas y Verduras (ID: cat_675ca59cb8ad9, slug: frutas-y-verduras)
    [merged_categories] => Array
        (
            [0] => Frutas Y Verduras (ID: cat_675ca5a1b02f0, slug: frutas-y-verduras-2)
            [1] => FrutasYVerduras (ID: cat_675ca5a8ac9a8, slug: frutasyverduras)
        )
    [category_mappings_updated] => 23
    [brand_categories_updated] => 5
    [products_updated] => 187
    [categories_deleted] => 2
)
```

### Ejemplo 2: Desde CLI (Recomendado)

```bash
# Simular fusión (dry-run)
php com zippy category merge \
  --target=cat_675ca59cb8ad9 \
  --sources=cat_675ca5a1b02f0,cat_675ca5a8ac9a8 \
  --dry-run

# Ejecutar fusión real
php com zippy category merge \
  --target=cat_675ca59cb8ad9 \
  --sources=cat_675ca5a1b02f0,cat_675ca5a8ac9a8
```

**Output CLI:**
```
╔═══════════════════════════════════════════════════════════════════╗
║     FUSIÓN DE CATEGORÍAS - ZIPPY                                 ║
╚═══════════════════════════════════════════════════════════════════╝

📌 Categoría DESTINO (se mantendrá):
   • Frutas y Verduras (ID: cat_675ca59cb8ad9, slug: frutas-y-verduras)

🗑️  Categorías a FUSIONAR (se eliminarán):
   • Frutas Y Verduras (ID: cat_675ca5a1b02f0, slug: frutas-y-verduras-2)
   • FrutasYVerduras (ID: cat_675ca5a8ac9a8, slug: frutasyverduras)

⚠️  Esta acción actualizará referencias en:
   - category_mappings
   - brand_categories
   - products (campo JSON 'categories')
   - Las categorías origen se eliminarán (soft delete)

╔═══════════════════════════════════════════════════════════════════╗
║  RESULTADO DE LA FUSIÓN                                          ║
╚═══════════════════════════════════════════════════════════════════╝

✅ Categoría definitiva: Frutas y Verduras (ID: cat_675ca59cb8ad9, slug: frutas-y-verduras)

📊 Estadísticas:
   • Categorías fusionadas: 2
   • category_mappings actualizados: 23
   • brand_categories actualizados: 5
   • Productos actualizados: 187

✅ Fusión completada exitosamente
```

---

## ✅ Validaciones

Antes de ejecutar la fusión, el método valida:

1. **Mínimo 2 categorías** - Se requieren al menos 2 IDs
2. **Categoría destino existe** - La primera categoría debe existir y no estar eliminada
3. **Categorías origen existen** - Todas las categorías origen deben existir
4. **No están eliminadas** - Ninguna puede tener `deleted_at` establecido

Si alguna validación falla, lanza `\Exception` con mensaje descriptivo.

---

## 🔍 Detección de Duplicados

### Método Manual (SQL)

```sql
-- Buscar categorías con nombres similares
SELECT id, name, slug, deleted_at
FROM categories
WHERE name LIKE '%Frutas%'
  AND deleted_at IS NULL
ORDER BY name;
```

### Con `php com zippy`

```bash
# Listar todas las categorías
php com zippy category all

# Ver árbol jerárquico
php com zippy category tree

# Listar categorías raw detectadas
php com zippy category list_raw --limit=100
```

---

## ⚠️ Consideraciones Importantes

### 1. **Operación Irreversible (Soft Delete)**

Las categorías origen se marcan como eliminadas (`deleted_at = NOW()`). Para recuperarlas:

```sql
UPDATE categories
SET deleted_at = NULL
WHERE id = 'cat_xxx';
```

### 2. **Rendimiento con Muchos Productos**

Si tienes muchos productos (>100,000), el proceso puede tardar varios minutos porque:
- Lee todos los productos con categorías
- Decodifica JSON
- Actualiza solo los que contienen slugs afectados

**Optimización futura:** Usar `JSON_CONTAINS()` en MySQL para filtrar productos.

### 3. **Transacciones**

Actualmente **NO** usa transacciones. Si falla a mitad del proceso:
- Los cambios previos quedan aplicados
- Puedes volver a ejecutar (es idempotente)

**Recomendación:** Siempre usar `--dry-run` primero para verificar.

### 4. **Duplicados en products**

Si un producto ya tenía ambos slugs:
```json
{"categories": ["frutas-y-verduras", "frutas-y-verduras-2"]}
```

Después de la fusión quedará sin duplicados:
```json
{"categories": ["frutas-y-verduras"]}
```

---

## 🧪 Testing

### Test con Categorías de Prueba

```bash
# 1. Crear categorías de prueba
php com zippy category create --name="Test Principal" --slug=test-principal
# Output: cat_xxx

php com zippy category create --name="Test Dup 1" --slug=test-dup-1
# Output: cat_yyy

php com zippy category create --name="Test Dup 2" --slug=test-dup-2
# Output: cat_zzz

# 2. Simular fusión
php com zippy category merge --target=cat_xxx --sources=cat_yyy,cat_zzz --dry-run

# 3. Ejecutar fusión
php com zippy category merge --target=cat_xxx --sources=cat_yyy,cat_zzz

# 4. Limpiar
php com sql statement "DELETE FROM categories WHERE id IN ('cat_xxx', 'cat_yyy', 'cat_zzz')" --connection=zippy --force
```

---

## 📊 Casos de Uso Comunes

### Caso 1: Normalización Post-Importación

Después de importar categorías desde un sistema externo:

```bash
# 1. Listar todas las categorías
php com zippy category all

# 2. Identificar duplicados manualmente
# Ejemplo: "Gourmet Food", "gourmetfood", "Comida Gourmet"

# 3. Fusionar
php com zippy category merge \
  --target=cat_comida_gourmet \
  --sources=cat_gourmet_food,cat_gourmetfood
```

### Caso 2: Consolidación de Variantes

Cuando hay múltiples variantes del mismo concepto:

```php
// Categorías encontradas:
// - "Lácteos" (definitiva)
// - "Lacteos" (sin tilde)
// - "Productos Lácteos" (más específica)

CategoryUtils::merge(
    'cat_lacteos',           // Definitiva
    'cat_lacteos_sin_tilde',
    'cat_productos_lacteos'
);
```

---

## 📚 Ver También

- **Guía de Buenas Prácticas:** `docs/packages/zippy/buenas-practicas-categorias.md`
- **Reporte category_mappings:** `docs/packages/zippy/category_mappings-reporte.md`
- **CategoryMapper:** `docs/packages/zippy/CategoryMapper.md`
- **Comandos CLI:** `php com zippy help`

---

## 🔄 Changelog

### 2025-12-07 - v1.0
- ✅ Implementación inicial de `CategoryUtils::merge()`
- ✅ Actualización de `category_mappings` (por slug y por ID)
- ✅ Actualización de `brand_categories` (por category_id)
- ✅ Actualización de productos (campo JSON 'categories')
- ✅ Soft delete de categorías origen
- ✅ Comando CLI `php com zippy category merge`
- ✅ Modo `--dry-run` para simulación
- ✅ Validaciones de integridad
- ✅ Estadísticas detalladas de la fusión

---

**Autor:** Pablo Bozzolo (boctulus)
**Software Architect**
**Última actualización:** 2025-12-07

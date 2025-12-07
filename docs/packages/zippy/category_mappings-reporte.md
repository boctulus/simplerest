# Reporte: category_mappings - Sistema de Aliases de Categorías

**Autor:** Pablo Bozzolo (boctulus)
**Fecha:** 2025-12-07
**Versión:** 1.0

---

## 📋 Resumen Ejecutivo

La tabla `category_mappings` es **PARTE ACTIVA Y FUNDAMENTAL** del flujo de categorización en Zippy. Funciona como un **sistema de caché de aliases** que mejora el rendimiento y reduce llamadas costosas al LLM.

**Estado actual (post-limpieza):**
- ✅ 2,021 mappings totales
- ✅ 99.7% válidos (2,015 mappings)
- ✅ 0% con slugs inexistentes (corregido)
- ✅ 0.3% borderline (6 mappings, probablemente correctos)

---

## 🎯 Propósito de category_mappings

### Función Principal

`category_mappings` actúa como **tabla de aliases/caché** para mapear valores crudos (`raw_value`) de categorías a slugs normalizados. Esto permite:

1. **Evitar re-procesamiento**: Si ya categorizamos "ACEITUNAS" antes, no necesitamos volver a preguntarle al LLM
2. **Consistencia**: Garantiza que el mismo input siempre retorne la misma categoría
3. **Performance**: Reduce latencia y costo de llamadas al LLM

### Estructura de la Tabla

```sql
CREATE TABLE category_mappings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raw_value VARCHAR(255),          -- Valor crudo original (Ej: "ACEITUNAS")
    normalized VARCHAR(255),         -- Valor normalizado (Ej: "aceitunas")
    category_id VARCHAR(50),         -- ID de la categoría (puede ser NULL)
    category_slug VARCHAR(100),      -- Slug de la categoría (Ej: "frutas-y-verduras")
    source VARCHAR(50),              -- Origen: 'llm', 'neural', 'static', etc.
    created_at DATETIME,
    updated_at DATETIME,
    deleted_at DATETIME DEFAULT NULL,
    KEY idx_normalized (normalized),
    KEY idx_category_slug (category_slug)
);
```

---

## 🔄 Flujo de Uso en CategoryMapper

### 1. Búsqueda de Categoría: `findCategory()`

```php
public static function findCategory(string $category): ?array
{
    $normalized = Strings::slug($category);

    // PASO 1: Buscar en tabla 'categories' por slug exacto
    $cat = DB::selectOne("SELECT id, slug, name FROM categories
                          WHERE slug = ? AND deleted_at IS NULL", [$normalized]);

    if ($cat) {
        return [
            'category_slug' => $cat['slug'],
            'found_in' => 'categories'
        ];
    }

    // PASO 2: Buscar en 'category_mappings' por normalized
    $map = DB::selectOne("SELECT category_slug FROM category_mappings
                          WHERE normalized = ? AND deleted_at IS NULL", [$normalized]);

    if ($map) {
        return [
            'category_slug' => $map['category_slug'],
            'found_in' => 'mappings'  // ← ENCONTRADO EN CACHE
        ];
    }

    return null; // No encontrado → procesar con LLM
}
```

**Orden de búsqueda:**
1. Buscar en `categories` por slug exacto
2. Si no existe, buscar en `category_mappings` por normalized
3. Si tampoco existe, se ejecuta estrategia LLM/Neural

### 2. Guardado de Alias: `saveCategoryAlias()`

Cada vez que el sistema resuelve una categoría (via LLM, Neural, etc.), guarda el mapping:

```php
public static function saveCategoryAlias(string $category_slug, string $raw_value, ?string $source = null): void
{
    $normalized = Strings::slug($raw_value);

    // Verificar si ya existe
    $exists = DB::selectOne("SELECT id FROM category_mappings
                             WHERE normalized = ? AND category_slug = ?",
                             [$normalized, $category_slug]);

    if ($exists) {
        return; // Ya existe, no duplicar
    }

    // Insertar nuevo alias
    DB::insert("INSERT INTO category_mappings
                (raw_value, normalized, category_slug, source, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())",
                [$raw_value, $normalized, $category_slug, $source]);
}
```

### 3. Llamadas a `saveCategoryAlias()` en `resolve()`

Dentro del método `CategoryMapper::resolve()`, se guarda el alias en **varios puntos**:

**Línea 283:** Cuando LLM sugiere crear nueva categoría pero el slug ya existe
```php
static::saveCategoryAlias($newSlug, $raw, $strategyKey);
```

**Línea 305:** Cuando se crea una nueva categoría
```php
static::saveCategoryAlias($newSlug, $raw, $strategyKey);
```

**Línea 324:** Cuando LLM retorna un slug existente
```php
static::saveCategoryAlias($returnedSlug, $raw, $strategyKey);
```

**Línea 340:** Cuando se encuentra coincidencia por nombre
```php
static::saveCategoryAlias($slug, $raw, $strategyKey);
```

---

## 📊 Estadísticas Actuales (Post-Limpieza)

### Distribución de Mappings por Categoría

| Categoría | Mappings | % del Total |
|-----------|----------|-------------|
| almacen | 608 | 30.1% |
| bebidas | 352 | 17.4% |
| higiene | 189 | 9.4% |
| panaderia | 179 | 8.9% |
| frutas-y-verduras | 156 | 7.7% |
| limpieza | 141 | 7.0% |
| golosinas | 121 | 6.0% |
| electro | 119 | 5.9% |
| congelados | 70 | 3.5% |
| infusiones | 32 | 1.6% |
| embutidos | 23 | 1.1% |
| lacteos | 15 | 0.7% |
| frescos | 8 | 0.4% |
| comida-gourmet | 5 | 0.2% |
| otros | 1 | 0.05% |

### Distribución por Source (Origen)

```sql
SELECT source, COUNT(*) as total
FROM category_mappings
WHERE deleted_at IS NULL
GROUP BY source
ORDER BY total DESC;
```

Ejemplos de values en `source`:
- `llm` - Categorizado por LLM (Ollama)
- `neural` - Categorizado por matching neural
- `static` - Mapping estático/manual
- `NULL` - Legacy sin source registrado

---

## 🧹 Limpieza Realizada (2025-12-07)

### Problemas Detectados

**Antes de la limpieza:**
- 2,050 mappings totales
- 9 con slugs inexistentes (0.44%)
- 30 sin sentido (1.46%)
- 2,011 válidos (98.1%)

### Correcciones Aplicadas

#### 1. Slugs Inexistentes → Corregidos (9 mappings)

| Slug Incorrecto | Slug Correcto | Mappings |
|----------------|---------------|----------|
| `gourmetfood` | `comida-gourmet` | 5 |
| `frutas y verduras` | `frutas-y-verduras` | 2 |
| `premium snacks and treats category` | `golosinas` | 2 |

#### 2. Mappings Sin Sentido → Eliminados (29 mappings)

Patrones eliminados:
- Productos de higiene (Rexona, etc.) → golosinas (10 eliminados)
- Productos electrónicos (PC, calefactores) → frutas/golosinas (2 eliminados)
- Dulce de leche/lácteos → frutas-y-verduras (12 eliminados)
- Galletas → frutas-y-verduras (5 eliminados)

**Después de la limpieza:**
- 2,021 mappings totales
- 0 con slugs inexistentes (0%) ✅
- 6 borderline (0.3%) - probablemente correctos
- 2,015 válidos (99.7%) ✅

---

## 🔍 Métodos que NO se Usan

### `getCategoryAliases()` - NO USADO

```php
public static function getCategoryAliases(string $category_slug): array
{
    // ... obtiene todos los aliases de una categoría
}
```

**Estado:** Método definido pero **NUNCA llamado** en la codebase.

**Recomendación:** Mantener por si se necesita en futuro para:
- Debugging (ver qué raw_values mapean a una categoría)
- Reportes
- Consolidación de duplicados

---

## ✅ Buenas Prácticas para Mantener category_mappings

### 1. NUNCA Crear Mappings Manualmente con Slugs Incorrectos

❌ **MAL:**
```php
DB::insert("INSERT INTO category_mappings (raw_value, normalized, category_slug)
            VALUES ('Aceitunas', 'aceitunas', 'frutas y verduras')"); // Slug con espacio
```

✅ **BIEN:**
```php
CategoryMapper::saveCategoryAlias('frutas-y-verduras', 'Aceitunas', 'manual');
// Usa Strings::slug() internamente
```

### 2. SIEMPRE Usar `CategoryMapper::saveCategoryAlias()`

Este método garantiza:
- Normalización correcta con `Strings::slug()`
- No duplicar mappings existentes
- Registrar source (origen)

### 3. Validar Mappings Periódicamente

```bash
# Ejecutar auditoría mensual
php scripts/tmp/analyze_category_mappings.php
```

### 4. NO Eliminar Mappings Sin Analizar Impacto

Los mappings son un caché valioso. Eliminarlos significa:
- Volver a procesar con LLM (costo/latencia)
- Posible inconsistencia en categorizaciones

---

## 📈 Comandos Útiles

### Ver Mappings de una Categoría

```bash
php com sql select "SELECT raw_value, source FROM category_mappings WHERE category_slug = 'frutas-y-verduras' LIMIT 20" --connection=zippy
```

### Contar Mappings por Source

```bash
php com sql select "SELECT source, COUNT(*) as total FROM category_mappings WHERE deleted_at IS NULL GROUP BY source ORDER BY total DESC" --connection=zippy
```

### Ver Mappings Recientes

```bash
php com sql select "SELECT raw_value, category_slug, source FROM category_mappings ORDER BY created_at DESC LIMIT 20" --connection=zippy
```

### Buscar Mappings por Patrón

```bash
php com sql select "SELECT raw_value, category_slug FROM category_mappings WHERE raw_value LIKE '%PEPSI%'" --connection=zippy
```

---

## 🎯 Conclusión

`category_mappings` es **ACTIVA Y ESENCIAL** para el sistema de categorización:

✅ **Sí, se usa** - En `findCategory()` y `saveCategoryAlias()`
✅ **Sí, tiene mantenimiento** - Se actualizan constantemente vía `saveCategoryAlias()`
✅ **Sí, es importante** - Caché que mejora performance y consistencia
✅ **Estado actual** - 99.7% válido después de limpieza

**Recomendación:** **MANTENER** y seguir usando. Ejecutar auditorías periódicas para detectar anomalías.

---

## 📚 Referencias

- **CategoryMapper.php:** `packages/boctulus/zippy/src/Libs/CategoryMapper.php`
- **Guía de Buenas Prácticas:** `docs/packages/zippy/buenas-practicas-categorias.md`
- **Comando de Análisis:** `scripts/tmp/analyze_category_mappings.php` (temporal)
- **Script de Limpieza:** `scripts/tmp/consolidate_category_mappings_sql.php` (temporal)

---

**Autor:** Pablo Bozzolo (boctulus)
**Software Architect**
**Última actualización:** 2025-12-07

# Sistema Híbrido de Pesos Neuronales - Guía Completa

**Fecha**: 2025-12-05
**Autor**: Pablo Bozzolo (boctulus)
**Package**: boctulus/zippy
**Versión**: 2.0

---

## 📋 Tabla de Contenidos

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Comandos CLI](#comandos-cli)
4. [Uso del Sistema](#uso-del-sistema)
5. [Gestión de Pesos](#gestión-de-pesos)
6. [Troubleshooting](#troubleshooting)
7. [Mejores Prácticas](#mejores-prácticas)

---

## Resumen Ejecutivo

### ¿Qué es el Sistema Híbrido?

El sistema híbrido de pesos permite gestionar los pesos de la red neuronal desde **base de datos** en lugar de tenerlos hardcoded en el código PHP, manteniendo un fallback automático para backward compatibility.

### Ventajas Clave

✅ **Pesos editables** vía SQL o futuro UI
✅ **Versionado** de cambios en BD
✅ **Preparado para aprendizaje** automático
✅ **Backward compatibility** (fallback a hardcoded)
✅ **Auditoría** con `usage_count` y `last_used_at`
✅ **A/B testing** de diferentes configuraciones de pesos

---

## Arquitectura del Sistema

### Orden de Prioridad de Carga

```
1. neural_weights (BD)
   ↓ (si vacía)
2. Pesos hardcoded (fallback)
   ↓ (siempre)
3. category_mappings (peso 1.0 - override)
```

### Diagrama de Flujo

```
┌─────────────────────────────────┐
│  NeuralMatchingStrategy         │
│  Constructor                    │
└────────────┬────────────────────┘
             │
             ↓
┌────────────────────────────────┐
│  loadWeights()                 │
└────────────┬───────────────────┘
             │
             ↓
    ┌────────────────┐
    │ ¿BD tiene      │
    │ datos?         │
    └────┬───────────┘
         │
    ┌────┴────┐
    │ SÍ      │ NO
    ↓         ↓
┌───────┐  ┌──────────────┐
│ Cargar│  │ Usar pesos   │
│ desde │  │ hardcoded    │
│ BD    │  │ (fallback)   │
└───┬───┘  └──────┬───────┘
    │             │
    └─────┬───────┘
          ↓
    ┌─────────────────────┐
    │ Cargar category_    │
    │ mappings (peso 1.0) │
    └─────────────────────┘
```

### Tabla: neural_weights

```sql
CREATE TABLE neural_weights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    word VARCHAR(100) NOT NULL,
    category_slug VARCHAR(100) NOT NULL,
    weight DECIMAL(4,3) DEFAULT 0.500,
    source VARCHAR(20),  -- 'hardcoded', 'manual', 'trained', 'learned'
    usage_count INT DEFAULT 0,
    last_used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,

    INDEX idx_word (word),
    INDEX idx_category (category_slug),
    UNIQUE KEY uk_word_category (word, category_slug)
);
```

---

## Comandos CLI

### 1. Poblar Base de Datos (Seed)

```bash
# Primera vez - poblar desde pesos hardcoded
php com zippy weights seed

# Sobrescribir pesos existentes
php com zippy weights seed --force
```

**Salida esperada**:
```
🧠 Poblando tabla neural_weights...

📂 electro (electro): 19 palabras
📂 panaderia (panaderia): 16 palabras
📂 bebidas (bebidas): 11 palabras
...

✅ Seed completado: 127 pesos insertados
   Total categorías procesadas: 9
```

### 2. Listar Pesos

```bash
# Listar todos los pesos (primeros 100)
php com zippy weights list

# Listar más resultados
php com zippy weights list --limit=200

# Listar pesos de una categoría específica
php com zippy weights list --category=frutas-y-verduras

# Listar pesos de categoría con límite personalizado
php com zippy weights list --category=electro --limit=50
```

**Salida esperada**:
```
📊 Pesos en neural_weights
─────────────────────────────────────────
fruta                frutas-y-verduras         0.800 (source: hardcoded, used: 0x)
frutas               frutas-y-verduras         0.800 (source: hardcoded, used: 0x)
notebook             electro                   0.900 (source: hardcoded, used: 0x)
...

📈 Total en BD: 127 pesos
```

### 3. Limpiar Tabla

```bash
# Intentar limpiar (pide confirmación)
php com zippy weights clear

# Limpiar con confirmación
php com zippy weights clear --confirm
```

**Salida esperada**:
```
✅ Tabla neural_weights limpiada (127 registros eliminados)
```

---

## Uso del Sistema

### Inicialización Automática

La estrategia `NeuralMatchingStrategy` carga automáticamente los pesos al instanciarse:

```php
use Boctulus\Zippy\Strategies\NeuralMatchingStrategy;

// La inicialización carga automáticamente:
// 1. Pesos desde BD (si existen)
// 2. Fallback a hardcoded (si BD vacía)
// 3. Mappings manuales (siempre)
$strategy = new NeuralMatchingStrategy();
```

### Logs de Diagnóstico

El sistema genera logs automáticos en tiempo real:

```
NeuralMatchingStrategy: Loaded 667 stop words from file
NeuralMatchingStrategy: Loading weights from database (neural_weights table)
NeuralMatchingStrategy: Loaded 127 weights from database
NeuralMatchingStrategy: Total 245 words loaded (source: database + mappings)
```

O si la BD está vacía:

```
NeuralMatchingStrategy: Database empty, using hardcoded weights as fallback
NeuralMatchingStrategy: Total 245 words loaded (source: hardcoded + mappings)
```

### Ejemplo de Clasificación

```php
// Obtener categorías disponibles
$categories = DB::select("SELECT id, slug FROM categories");
$availableCategories = [];
foreach ($categories as $cat) {
    $availableCategories[$cat->slug] = ['id' => $cat->id];
}

// Clasificar producto
$text = "ENSALADA DE FRUTAS MIXTAS 500G";
$result = $strategy->match($text, $availableCategories);

if ($result) {
    echo "Categoría: {$result['category']}\n";
    echo "Score: {$result['score']}\n";
    echo "Palabras: " . implode(', ', $result['matched_words']) . "\n";
    // Salida:
    // Categoría: frutas-y-verduras
    // Score: 0.8
    // Palabras: frutas
}
```

---

## Gestión de Pesos

### Agregar Nueva Palabra

```sql
-- Insertar manualmente un nuevo peso
INSERT INTO neural_weights
(word, category_slug, weight, source, created_at, updated_at)
VALUES
('mango', 'frutas-y-verduras', 0.85, 'manual', NOW(), NOW());
```

### Modificar Peso Existente

```sql
-- Aumentar peso de una palabra
UPDATE neural_weights
SET weight = 0.95,
    source = 'manual',
    updated_at = NOW()
WHERE word = 'notebook'
AND category_slug = 'electro';
```

### Eliminar Palabra

```sql
-- Eliminar palabra específica
DELETE FROM neural_weights
WHERE word = 'cable'
AND category_slug = 'electro';
```

### Agregar Categoría Completa

```sql
-- Insertar múltiples palabras para nueva categoría
INSERT INTO neural_weights
(word, category_slug, weight, source, created_at, updated_at)
VALUES
('leche', 'lacteos', 0.9, 'manual', NOW(), NOW()),
('yogur', 'lacteos', 0.9, 'manual', NOW(), NOW()),
('queso', 'lacteos', 0.9, 'manual', NOW(), NOW()),
('manteca', 'lacteos', 0.8, 'manual', NOW(), NOW());
```

### Clonar Pesos de una Categoría

```sql
-- Copiar pesos de una categoría a otra
INSERT INTO neural_weights (word, category_slug, weight, source, created_at, updated_at)
SELECT word, 'nueva-categoria', weight, 'cloned', NOW(), NOW()
FROM neural_weights
WHERE category_slug = 'categoria-origen';
```

---

## Troubleshooting

### Problema: Sistema usa hardcoded en vez de BD

**Síntoma**: Logs muestran "using hardcoded weights as fallback"

**Causa**: Tabla `neural_weights` está vacía

**Solución**:
```bash
php com zippy weights seed
```

---

### Problema: Nueva palabra no funciona

**Síntoma**: Palabra agregada a BD pero no clasifica productos

**Diagnóstico**:
```sql
-- Verificar que la palabra existe
SELECT * FROM neural_weights
WHERE word = 'tu_palabra';

-- Verificar formato (debe ser minúsculas)
SELECT word FROM neural_weights
WHERE word LIKE '%TU_PALABRA%';
```

**Solución**:
```sql
-- Asegurarse que esté en minúsculas
UPDATE neural_weights
SET word = LOWER(word);

-- Verificar que category_slug existe
SELECT slug FROM categories
WHERE slug = 'tu-categoria';
```

---

### Problema: Pesos duplicados

**Síntoma**: Error "Duplicate entry for key 'uk_word_category'"

**Causa**: Intentar insertar combinación palabra-categoría que ya existe

**Solución**:
```sql
-- Verificar si existe
SELECT * FROM neural_weights
WHERE word = 'palabra'
AND category_slug = 'categoria';

-- Actualizar en vez de insertar
UPDATE neural_weights
SET weight = 0.95
WHERE word = 'palabra'
AND category_slug = 'categoria';

-- O usar INSERT ... ON DUPLICATE KEY UPDATE
INSERT INTO neural_weights (word, category_slug, weight, source, created_at, updated_at)
VALUES ('palabra', 'categoria', 0.95, 'manual', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    weight = 0.95,
    source = 'manual',
    updated_at = NOW();
```

---

### Problema: Categoría no se encuentra

**Síntoma**: Warning "Categoría 'xxx' no encontrada en BD, saltando..."

**Causa**: La categoría no existe en tabla `categories`

**Solución**:
```bash
# Crear la categoría primero
php com zippy category create --name="Mi Categoría" --slug=mi-categoria

# Luego agregar pesos
php com zippy weights seed
```

---

## Mejores Prácticas

### 1. Backup Antes de Cambios Importantes

```bash
# Crear backup de la base de datos
mysqldump -u root zippy > backups/zippy_backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Testear Cambios en Dry-Run

```bash
# Antes de aplicar cambios masivos, probar con dry-run
php com zippy product process --limit=10 --dry-run
```

### 3. Monitorear Logs

```bash
# Ver logs en tiempo real
tail -f logs/neural_matching_*.log
```

### 4. Convenciones de Nomenclatura

- **Palabras**: Siempre en **minúsculas**
- **Category slugs**: Formato `kebab-case`
- **Source values**: `hardcoded`, `manual`, `trained`, `learned`

### 5. Pesos Recomendados

| Tipo de Match | Peso Recomendado |
|---------------|------------------|
| Palabra muy específica (ej: "notebook") | 0.9 - 1.0 |
| Palabra específica (ej: "lactal") | 0.8 - 0.9 |
| Palabra genérica (ej: "frutas") | 0.7 - 0.8 |
| Palabra ambigua (ej: "cable") | 0.6 - 0.7 |

### 6. Orden de Inserción

1. Crear categorías primero
2. Poblar `neural_weights`
3. Agregar `category_mappings` (si es necesario)
4. Probar clasificación
5. Ajustar pesos según resultados

---

## Workflow de Producción

### Setup Inicial

```bash
# 1. Verificar migración ejecutada
php com sql describe table zippy.neural_weights

# 2. Poblar pesos desde hardcoded
php com zippy weights seed

# 3. Verificar datos cargados
php com zippy weights list --limit=20

# 4. Crear backup
mysqldump -u root zippy > backups/zippy_initial_weights.sql
```

### Actualización de Pesos

```bash
# 1. Backup previo
mysqldump -u root zippy neural_weights > backups/neural_weights_$(date +%Y%m%d).sql

# 2. Aplicar cambios vía SQL
mysql -u root zippy < updates/new_weights.sql

# 3. Verificar cambios
php com zippy weights list --category=categoria-modificada

# 4. Probar clasificación
php com zippy product process --limit=100 --dry-run
```

### Rollback

```bash
# Si algo sale mal, restaurar desde backup
mysql -u root zippy < backups/neural_weights_20251205.sql

# Verificar restauración
php com zippy weights list
```

---

## Estadísticas Actuales

### Distribución de Pesos (Post-Seed)

| Categoría | Palabras | % del Total |
|-----------|----------|-------------|
| almacen | 27 | 21.3% |
| electro | 19 | 15.0% |
| panaderia | 16 | 12.6% |
| frutas-y-verduras | 15 | 11.8% |
| bebidas | 11 | 8.7% |
| golosinas | 11 | 8.7% |
| limpieza | 10 | 7.9% |
| embutidos | 9 | 7.1% |
| congelados | 9 | 7.1% |
| **TOTAL** | **127** | **100%** |

---

## Referencias

### Archivos Relacionados

| Archivo | Descripción |
|---------|-------------|
| `src/Strategies/NeuralMatchingStrategy.php` | Lógica de carga híbrida |
| `src/Commands/ZippyCommand.php` | Comandos CLI de weights |
| `database/migrations/2025_12_05_161149088_neural_weights.php` | Migración de tabla |
| `src/Models/NeuralWeights.php` | Modelo de datos |
| `docs/neural-network-implementation-report.md` | Reporte técnico completo |

### Comandos Útiles

```bash
# Ver ayuda de weights
php com zippy weights

# Ver ayuda general de zippy
php com zippy help

# Ver estructura de tabla
php com sql describe table zippy.neural_weights

# Contar registros
php com sql select "SELECT COUNT(*) as total FROM neural_weights" --connection=zippy
```

---

**Fin del Documento**

**Última actualización**: 2025-12-05
**Mantenido por**: Pablo Bozzolo (boctulus)

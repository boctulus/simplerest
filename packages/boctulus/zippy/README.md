# Zippy - Category Mapping System

Sistema de mapeo inteligente de categorías para productos usando LLM y matching difuso.

## 📋 Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Arquitectura](#arquitectura)
- [Comandos CLI](#comandos-cli)
- [Flujos de Trabajo](#flujos-de-trabajo)
- [Configuración](#configuración)
- [Estrategias de Matching](#estrategias-de-matching)

## Requisitos Previos

- Base de datos `zippy` migrada (ejecuta las migrations del paquete)
- Ollama corriendo localmente para usar estrategia LLM
- PHP 7.4+ con extensiones necesarias
- Composer dependencies instaladas

## Arquitectura

### Componentes Principales

- **CategoryMapper** (`src/Libs/CategoryMapper.php`): Lógica central de resolución y mapeo
- **LLMMatchingStrategy** (`src/Strategies/LLMMatchingStrategy.php`): Estrategia basada en LLM
- **FuzzyMatchingStrategy** (`src/Strategies/FuzzyMatchingStrategy.php`): Estrategia de matching difuso
- **ZippyCommand** (`src/Commands/ZippyCommand.php`): Interfaz CLI para gestión

### Estructura de Base de Datos

```sql
-- Tabla: categories
CREATE TABLE categories (
  id VARCHAR(21) PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) UNIQUE,
  parent_id VARCHAR(21),
  parent_slug VARCHAR(150),
  image_url VARCHAR(255),
  store_id VARCHAR(30),
  proposed_by ENUM('human', 'llm', 'neural network') DEFAULT 'llm',
  is_approved BOOLEAN DEFAULT FALSE,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  deleted_at TIMESTAMP NULL
);

-- Tabla: category_mappings (alias)
-- Almacena mappings de textos raw a categorías
```

## Comandos CLI

Todos los comandos siguen el patrón: `php com zippy <namespace> <comando> [opciones]`

### 📦 Namespace: product

#### `product process`
Procesa productos individualmente y actualiza sus categorías.

```bash
php com zippy product process --limit=100 --dry-run
```

**Opciones:**
- `--limit=N`: Cantidad de productos (default: 100)
- `--dry-run`: Modo simulación
- `--strategy=X`: llm|fuzzy

#### `product batch`
Procesamiento batch optimizado para grandes volúmenes.

```bash
php com zippy product batch --limit=1000 --only-unmapped --dry-run
```

**Opciones:**
- `--limit=N`: Cantidad de productos
- `--offset=N`: Offset para paginación
- `--only-unmapped`: Solo productos sin categorías
- `--dry-run`: Modo simulación

### 🏷️ Namespace: category

#### Gestión Básica

##### `category all`
Lista todas las categorías existentes.

```bash
php com zippy category all
```

##### `category list_raw`
Lista categorías raw detectadas en productos (campos catego_raw1/2/3).

```bash
php com zippy category list_raw --limit=100
```

Muestra formato: `[N] categoria_raw → slug_mapeado [Padre]`

##### `category create`
Crea una nueva categoría.

```bash
php com zippy category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
```

**Opciones:**
- `--name="X"`: Nombre (REQUERIDO)
- `--slug=X`: Slug (opcional, se genera del nombre)
- `--parent=X`: Slug del padre
- `--image_url=X`: URL de imagen
- `--store_id=X`: ID de tienda

##### `category set`
Establece o cambia el padre de una categoría.

```bash
php com zippy category set --slug=dairy.milk --parent=dairy
php com zippy category set --slug=dairy.milk --parent=NULL  # Desemparentar
```

#### Pruebas y Resolución

##### `category test`
Prueba mapeo de una categoría raw sin guardar.

```bash
php com zippy category test --raw="Aceites Y Condimentos" --strategy=llm
```

##### `category resolve`
Resuelve categoría usando LLM (texto suelto).

```bash
php com zippy category resolve --text="Leche entera 1L marca tradicional"
```

##### `category resolve_product`
Resuelve categorías para un producto completo.

```bash
php com zippy category resolve_product \
  --raw1="Leche entera" \
  --raw2="" \
  --description="Pack de 6 leches 1L" \
  --ean=7501234567890
```

##### `category create_mapping`
Crea un mapping (alias) manual.

```bash
php com zippy category create_mapping \
  --slug=dairy.milk \
  --raw="Leche entera 1L" \
  --source=mercado
```

#### Diagnóstico

##### `category find_missing_parents`
Encuentra categorías padre referenciadas que no existen.

```bash
php com zippy category find_missing_parents
```

##### `category find_orphans`
Encuentra categorías huérfanas (padre no existe).

```bash
php com zippy category find_orphans
```

##### `category report_issues`
Reporte completo de problemas de integridad.

```bash
php com zippy category report_issues
```

Genera reporte con:
- Padres faltantes
- Categorías huérfanas
- Resumen de problemas

##### `category generate_create_commands`
Genera comandos para crear categorías padre faltantes.

```bash
php com zippy category generate_create_commands
```

Output ejemplo:
```bash
# Commands to create missing parent categories:

php com zippy category create --name="Dairy" --slug=dairy
php com zippy category create --name="Bakery" --slug=bakery

# Total commands: 2
```

#### Utilidades

##### `category clear_cache`
Limpia el caché de CategoryMapper.

```bash
php com zippy category clear_cache
```

### 🤖 Namespace: ollama

#### `ollama test_strategy`
Lista modelos Ollama disponibles.

```bash
php com zippy ollama test_strategy
```

#### `ollama hard_tests`
Ejecuta pruebas hardcodeadas del LLM.

```bash
php com zippy ollama hard_tests
```

Ejecuta tests predefinidos con categorías de ejemplo para validar respuestas LLM.

## Flujos de Trabajo

### 🔹 Flujo 1: Setup Inicial y Diagnóstico

**Objetivo:** Verificar estado de categorías y corregir problemas estructurales.

```bash
# 1. Ver estado actual
php com zippy category all

# 2. Identificar problemas
php com zippy category report_issues

# 3. Generar comandos de corrección
php com zippy category generate_create_commands

# 4. Crear categorías faltantes (copiar y ejecutar output del paso 3)
php com zippy category create --name="Dairy" --slug=dairy
php com zippy category create --name="Bakery" --slug=bakery

# 5. Verificar corrección
php com zippy category report_issues
```

### 🔹 Flujo 2: Exploración y Testing

**Objetivo:** Explorar datos y probar estrategias de mapeo.

```bash
# 1. Ver categorías raw en productos
php com zippy category list_raw --limit=100

# 2. Probar mapeo de una categoría específica
php com zippy category test --raw="Aceites Y Condimentos"

# 3. Probar resolución con LLM
php com zippy category resolve --text="Leche entera 1L marca tradicional"

# 4. Validar respuestas LLM con tests predefinidos
php com zippy ollama hard_tests
```

### 🔹 Flujo 3: Procesamiento en Producción

**Objetivo:** Procesar productos y asignar categorías en producción.

```bash
# 1. Verificar integridad antes de procesar
php com zippy category report_issues

# 2. Prueba con pocos productos en dry-run
php com zippy product process --limit=10 --dry-run

# 3. Procesar batch pequeño real
php com zippy product process --limit=100

# 4. Procesar grandes volúmenes (solo sin mapear)
php com zippy product batch --limit=1000 --only-unmapped

# 5. Procesar todo el catálogo en batches
php com zippy product batch --limit=5000 --offset=0
php com zippy product batch --limit=5000 --offset=5000
# ...continuar con offsets
```

### 🔹 Flujo 4: Validación de LLM

**Objetivo:** Verificar configuración y respuestas del LLM.

```bash
# 1. Verificar modelos disponibles
php com zippy ollama test_strategy

# 2. Ejecutar tests predefinidos
php com zippy ollama hard_tests

# 3. Probar con categorías reales
php com zippy category test --raw="Aceites Y Condimentos" --strategy=llm

# 4. Probar resolución de producto completo
php com zippy category resolve_product \
  --raw1="Aceites" \
  --raw2="Condimentos" \
  --description="Aceite de oliva extra virgen 500ml"
```

## Configuración

### CategoryMapper

Configurar antes de usar:

```php
CategoryMapper::configure([
    'default_strategy' => 'llm',
    'strategies_order' => ['llm', 'fuzzy'],
    'llm_model' => 'qwen2.5:3b',
    'llm_temperature' => 0.2,
    'thresholds' => [
        'fuzzy' => 0.40,  // 40% similaridad mínima
        'llm' => 0.70,    // 70% confianza mínima
    ]
]);
```

### Opciones de Configuración

| Opción | Tipo | Default | Descripción |
|--------|------|---------|-------------|
| `default_strategy` | string | 'llm' | Estrategia por defecto |
| `strategies_order` | array | ['llm', 'fuzzy'] | Orden de estrategias a probar |
| `llm_model` | string | 'qwen2.5:3b' | Modelo Ollama a usar |
| `llm_temperature` | float | 0.2 | Temperatura del LLM (0-1) |
| `llm_verbose` | bool | false | Logging detallado |
| `thresholds` | array | - | Umbrales por estrategia |

## Estrategias de Matching

### LLM Strategy (Recomendada)

Usa modelos de lenguaje (Ollama) para clasificación inteligente.

**Ventajas:**
- Entiende contexto y sinónimos
- Puede sugerir nuevas categorías
- Alta precisión con buen prompt

**Desventajas:**
- Requiere Ollama corriendo
- Más lenta que fuzzy
- Consume recursos

**Configuración:**
```php
CategoryMapper::configure([
    'default_strategy' => 'llm',
    'llm_model' => 'qwen2.5:3b',
    'llm_temperature' => 0.2,
    'thresholds' => ['llm' => 0.70]
]);
```

### Fuzzy Strategy

Usa similaridad de texto (Levenshtein, etc).

**Ventajas:**
- Rápida y eficiente
- No requiere servicios externos
- Buena para typos y variaciones

**Desventajas:**
- No entiende contexto
- Requiere texto muy similar
- Solo matching exacto

**Configuración:**
```php
CategoryMapper::configure([
    'default_strategy' => 'fuzzy',
    'thresholds' => ['fuzzy' => 0.40]
]);
```

### Estrategia Híbrida (Recomendada)

Prueba LLM primero, fallback a fuzzy.

```php
CategoryMapper::configure([
    'strategies_order' => ['llm', 'fuzzy'],
    'thresholds' => [
        'llm' => 0.70,
        'fuzzy' => 0.40
    ]
]);
```

## Creación Automática de Categorías

Cuando LLM sugiere categorías nuevas:

1. `CategoryMapper` detecta `is_new: true` en respuesta LLM
2. Extrae `suggested_name` y `suggested_slug`
3. Crea nueva fila en tabla `categories` con:
   - `id`: `uniqid('cat_')`
   - `slug`: normalizado del suggested_slug
   - `proposed_by`: 'llm'
   - `is_approved`: false
4. Crea mapping automático en `category_mappings`

**Revisar categorías propuestas:**
```sql
SELECT * FROM categories 
WHERE proposed_by = 'llm' 
  AND is_approved = FALSE;
```

## Problemas Comunes

### LLM no disponible

**Síntoma:** Comandos LLM fallan con error de conexión.

**Solución:**
```bash
# Verificar Ollama
ollama list

# Iniciar Ollama si no está corriendo
ollama serve

# Descargar modelo si es necesario
ollama pull qwen2.5:3b
```

### Respuestas LLM fuera de formato

**Síntoma:** `parseResponse` falla al extraer JSON.

**Solución:** Ajustar prompt en `LLMMatchingStrategy` o cambiar temperatura:

```php
CategoryMapper::configure([
    'llm_temperature' => 0.1  // Más determinista
]);
```

### Categorías huérfanas

**Síntoma:** `category find_orphans` muestra categorías sin padre válido.

**Solución:**
```bash
# 1. Ver reporte
php com zippy category report_issues

# 2. Crear padres faltantes
php com zippy category generate_create_commands

# 3. Ejecutar comandos generados
# ...

# 4. O actualizar hijos manualmente
php com zippy category set --slug=dairy.milk --parent=dairy
```

## Mejores Prácticas

1. **Siempre usar dry-run primero** al procesar productos en batch
2. **Verificar integridad** con `category report_issues` antes de procesar
3. **Procesar en lotes pequeños** inicialmente para validar calidad
4. **Revisar categorías propuestas por LLM** antes de aprobar
5. **Mantener umbrales conservadores** (>70% para LLM, >40% para fuzzy)
6. **Monitorear logs** en modo verbose para debugging

## Ayuda

Para ver ayuda completa en CLI:

```bash
php com zippy help
```

## Contribuir

Al agregar nuevos comandos:

1. Mantener patrón de namespaces (`product`, `category`, `ollama`)
2. Usar métodos protected con prefijo `{namespace}_`
3. Documentar opciones y ejemplos en `help()`
4. Actualizar este README


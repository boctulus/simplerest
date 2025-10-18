
# Zippy - Category mapping quick guide

Este README explica el flujo para probar CategoryMapper y las utilidades relacionadas.

## Requisitos previos
- Base de datos `zippy` migrada (ejecuta tus migrations del paquete).
- Ollama (u otro proveedor LLM configurado por `LLMFactory::ollama()`) corriendo localmente si vas a usar la estrategia `llm`.
- Asegúrate de que `Boctulus\LLMProviders\Factory\LLMFactory::ollama()` esté disponible y configurado.

## Archivos relevantes
- `src/Libs/CategoryMapper.php` — Lógica principal para resolver y crear mappings/categorías.
- `src/Strategies/LLMMatchingStrategy.php` — Estrategia LLM (parseo de respuesta ajustado para sugerencias de nuevas categorías).
- `src/Strategies/FuzzyMatchingStrategy.php` — Estrategia de matching difuso.
- `src/Commands/ZippyCommand.php` — Comandos CLI para gestionar categorías, productos y diagnósticos.
- `config/cli_routes.php` — Comandos CLI registrados (deprecados, usar ZippyCommand).

## Comandos CLI (orden recomendado de pruebas)

### Ver ayuda completa
```bash
php com zippy help
```

### 1. Listar categorías existentes
```bash
php com zippy category list_all
```
Lista todas las categorías de la tabla `categories`.

### 2. Listar categorías raw de productos
```bash
php com zippy category_list --limit=100
```
Lista categorías únicas extraídas de los campos `catego_raw1`, `catego_raw2`, `catego_raw3` de productos.

### 3. Crear una categoría manual (opcional)
```bash
php com zippy category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
```
**Opciones:**
- `--name="<nombre>"`: (Requerido) Nombre de la categoría
- `--slug=<slug>`: (Opcional) Slug de la categoría. Si no se especifica, se genera automáticamente del nombre
- `--parent=<slug>`: (Opcional) Slug del padre
- `--image_url=<url>`: (Opcional) URL de imagen
- `--store_id=<id>`: (Opcional) ID de tienda

### 4. Crear un mapping manual (alias)
```bash
php com zippy category create_mapping --slug=dairy.milk --raw="Leche entera 1L marca tradicional" --source=mercado
```
**Opciones:**
- `--slug=<slug>`: (Requerido) Slug de categoría existente
- `--raw="<texto>"`: (Requerido) Texto raw a mapear
- `--source=<fuente>`: (Opcional) Fuente del mapping

### 5. Probar mapeo de una categoría raw
```bash
php com zippy test_mapping --raw="Aceites Y Condimentos" --strategy=llm
```
**Opciones:**
- `--raw="<valor>"`: (Requerido) Texto de la categoría a probar
- `--strategy=<estrategia>`: (Opcional) `llm` o `fuzzy`. Por defecto: `llm`

### 6. Probar resolver con texto suelto (invoca LLM)
```bash
php com zippy category resolve --text="Leche entera 1L marca tradicional"
```
**Opciones:**
- `--text="<texto>"`: (Requerido) Texto a resolver

### 7. Probar resolver para un producto (slots + description)
```bash
php com zippy category resolve_product --raw1="Leche entera 1L" --raw2="" --description="Pack de 6 leches 1L"
```
**Opciones:**
- `--raw1="<texto>"`: Categoría raw 1
- `--raw2="<texto>"`: Categoría raw 2
- `--raw3="<texto>"`: Categoría raw 3
- `--description="<texto>"`: Descripción del producto
- `--ean=<ean>`: EAN del producto

### 8. Ejecutar pruebas duras del LLM (hard_tests)
```bash
php com zippy ollama hard_tests
```
Este comando ejecuta tests hardcodeados y muestra cada respuesta LLM (útil para debugging).
Asegúrate de que `LLMMatchingStrategy::isAvailable()` devuelva `true` (Ollama corriendo).

### 9. Listar modelos Ollama disponibles
```bash
php com zippy ollama test_strategy
```

## Comandos de diagnóstico de categorías

Estos comandos ayudan a identificar y solucionar problemas de integridad en la estructura de categorías:

### 1. Encontrar categorías padre faltantes
```bash
php com zippy category find_missing_parents
```
- Busca todos los `parent_slug` que se referencian pero no existen como categorías.
- Muestra cuántas categorías hijas tiene cada padre faltante.
- Útil para detectar padres que deberían crearse.

### 2. Encontrar categorías huérfanas
```bash
php com zippy category find_orphans
```
- Lista todas las categorías cuyo `parent_slug` no existe en la base de datos.
- Muestra el ID, slug, nombre y el padre faltante de cada categoría huérfana.
- Ayuda a identificar categorías que quedaron con referencias inválidas.

### 3. Reporte completo de problemas
```bash
php com zippy category report_issues
```
- Genera un reporte combinado con padres faltantes y categorías huérfanas.
- Incluye un resumen con totales y el estado general de integridad.
- Útil para tener una vista completa de todos los problemas.

### 4. Generar comandos de creación automática
```bash
php com zippy category generate_create_commands
```
- Analiza los padres faltantes y genera los comandos `php com zippy` necesarios para crearlos.
- Los comandos generados incluyen un nombre sugerido basado en el slug.
- Copia y ejecuta los comandos generados para resolver rápidamente los problemas.

### Ejemplo de flujo de diagnóstico y corrección

```bash
# 1. Revisar si hay problemas
php com zippy category report_issues

# 2. Generar comandos para crear categorías faltantes
php com zippy category generate_create_commands

# 3. Ejecutar los comandos generados (copiar y pegar cada línea)
php com zippy category create --name="Dairy" --slug=dairy
php com zippy category create --name="Bakery" --slug=bakery

# 4. Verificar que se resolvieron los problemas
php com zippy category report_issues
```

## Notas operativas
- **Creación automática de categorías:** Si LLM sugiere `is_new: true` con `sugested_name`, `CategoryMapper` creará una nueva fila en `categories` con `id = uniqid('cat_')`, slug normalizado y creará un `category_mappings` desde el `raw_value`.
- **Umbrales:** Por defecto LLM threshold = 0.7 (70%). Ajusta en `CategoryMapper::configure()` o pasando configuración en tus scripts antes de invocar resolve/resolveProduct.
- **Registro/Debug:** Si necesitas más verbosidad en LLM, crea la estrategia con `verbose=true` o ajusta `llm_verbose` en `CategoryMapper::configure`.

## Comandos de procesamiento en batch

### 1. Procesar categorías de productos
```bash
php com zippy process_categories --limit=100 --dry-run
```
**Opciones:**
- `--limit=<N>`: Limitar cantidad de productos
- `--offset=<N>`: Offset para paginación
- `--only-unmapped`: Solo productos sin categorías asignadas
- `--dry-run`: No guardar cambios (modo simulación)

### 2. Procesar productos y actualizar categorías
```bash
php com zippy products_process_categories --limit=100 --dry-run
```
**Opciones:**
- `--limit=<N>`: Limitar cantidad (default: 100)
- `--dry-run`: No guardar cambios
- `--strategy=<estrategia>`: Estrategia a usar

### 3. Limpiar caché
```bash
php com zippy clear_cache
```
Limpia el caché de CategoryMapper (⚠ pendiente implementar).

## Flujo recomendado en pruebas reales
1. Ejecuta `php com zippy category list_all` para comprobar el estado de las categorías.
2. Ejecuta `php com zippy category_list` para ver categorías raw de productos.
3. Inserta manualmente algunas categorías de referencia (si tu catálogo no las tiene).
4. Ejecuta `php com zippy category resolve` o `php com zippy test_mapping` para algunos `raw` representativos.
   - Si LLM sugiere categorías existentes, `CategoryMapper` guardará mappings automáticamente.
   - Si LLM sugiere nuevas categorías, las creará (y mapeará).
5. Revisa la tabla `category_mappings` y `categories` para confirmar resultados.
6. Ejecuta diagnósticos con `php com zippy category report_issues` para verificar integridad.
7. Corre `php com zippy process_categories` o `php com zippy products_process_categories` (si quieres procesar lotes) cuando estés satisfecho con la calidad.

## Problemas comunes
- LLM no disponible: los comandos LLM fallarán. Asegúrate de que Ollama corra y esté accesible.
- Respuestas LLM fuera de formato: la estrategia intenta extraer JSON del texto. Si tu LLM no respeta el formato, corrige el prompt o ajusta `parseResponse`.



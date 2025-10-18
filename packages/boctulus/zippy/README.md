
# Zippy - Category mapping quick guide

Este README explica el flujo para probar CategoryMapper y las utilidades relacionadas.

## Requisitos previos
- Base de datos `zippy` migrada (ejecuta tus migrations del paquete).
- Ollama (u otro proveedor LLM configurado por `LLMFactory::ollama()`) corriendo localmente si vas a usar la estrategia `llm`.
- Asegúrate de que `Boctulus\LLMProviders\Factory\LLMFactory::ollama()` esté disponible y configurado.

## Archivos relevantes
- `src/Libs/CategoryMapper.php` — Lógica principal para resolver y crear mappings/categorías.
- `src/Strategies/LLMMatchingStrategy.php` — Estrategia LLM (parseo de respuesta ajustado para sugerencias de nuevas categorías).
- `src/Controllers/CategoryController.php` — Métodos CLI para crear categorías, mappings y probar resolvers.
- `config/cli_routes.php` — Comandos CLI registrados.

## Comandos CLI (orden recomendado de pruebas)

1. **Listar categorías existentes**
   ```bash
   php com zippycart category list
   ```

2. **Crear una categoría manual (opcional)**
   ```bash
   php com zippycart category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
   ```
   - Si no pasas `--slug`, se normalizará `--name` como slug.

3. **Crear un mapping manual (alias)**
   ```bash
   php com zippycart category create_mapping --slug=dairy.milk --raw="Leche entera 1L marca tradicional" --source=mercado
   ```

4. **Probar resolver con texto suelto (invoca LLM)**
   ```bash
   php com zippycart category resolve --text="Leche entera 1L marca tradicional"
   ```
   - `CategoryMapper` está configurado por defecto para usar `llm` en primer lugar. Ajusta umbral si es necesario.

5. **Probar resolver para un producto (slots + description)**
   ```bash
   php com zippycart category resolve_product --raw1="Leche entera 1L" --raw2="" --description="Pack de 6 leches 1L"
   ```

6. **Ejecutar pruebas duras del LLM (hard_tests)**
   ```bash
   php com zippycart ollama hard_tests
   ```
   - Este comando ejecuta tests hardcodeados y hace `dd()` de cada respuesta LLM (útil para debugging).
   - Asegúrate de que `LLMMatchingStrategy::isAvailable()` devuelva `true` (Ollama up).

## Comandos de diagnóstico de categorías

Estos comandos ayudan a identificar y solucionar problemas de integridad en la estructura de categorías:

1. **Encontrar categorías padre faltantes**
   ```bash
   php com zippycart category find_missing_parents
   ```
   - Busca todos los `parent_slug` que se referencian pero no existen como categorías.
   - Muestra cuántas categorías hijas tiene cada padre faltante.
   - Útil para detectar padres que deberían crearse.

2. **Encontrar categorías huérfanas**
   ```bash
   php com zippycart category find_orphans
   ```
   - Lista todas las categorías cuyo `parent_slug` no existe en la base de datos.
   - Muestra el ID, slug, nombre y el padre faltante de cada categoría huérfana.
   - Ayuda a identificar categorías que quedaron con referencias inválidas.

3. **Reporte completo de problemas**
   ```bash
   php com zippycart category report_issues
   ```
   - Genera un reporte combinado con padres faltantes y categorías huérfanas.
   - Incluye un resumen con totales y el estado general de integridad.
   - Útil para tener una vista completa de todos los problemas.

4. **Generar comandos de creación automática**
   ```bash
   php com zippycart category generate_create_commands
   ```
   - Analiza los padres faltantes y genera los comandos `php com` necesarios para crearlos.
   - Los comandos generados incluyen un nombre sugerido basado en el slug.
   - Copia y ejecuta los comandos generados para resolver rápidamente los problemas.

### Ejemplo de flujo de diagnóstico y corrección:

```bash
# 1. Revisar si hay problemas
php com zippycart category report_issues

# 2. Generar comandos para crear categorías faltantes
php com zippycart category generate_create_commands

# 3. Ejecutar los comandos generados (copiar y pegar cada línea)
php com zippycart category create --name="Dairy" --slug=dairy
php com zippycart category create --name="Bakery" --slug=bakery

# 4. Verificar que se resolvieron los problemas
php com zippycart category report_issues
```

## Notas operativas
- **Creación automática de categorías:** Si LLM sugiere `is_new: true` con `sugested_name`, `CategoryMapper` creará una nueva fila en `categories` con `id = uniqid('cat_')`, slug normalizado y creará un `category_mappings` desde el `raw_value`.
- **Umbrales:** Por defecto LLM threshold = 0.7 (70%). Ajusta en `CategoryMapper::configure()` o pasando configuración en tus scripts antes de invocar resolve/resolveProduct.
- **Registro/Debug:** Si necesitas más verbosidad en LLM, crea la estrategia con `verbose=true` o ajusta `llm_verbose` en `CategoryMapper::configure`.

## Flujo recomendado en pruebas reales
1. Ejecuta `category list` para comprobar el estado.
2. Inserta manualmente algunas categorías de referencia (si tu catálogo no las tiene).
3. Ejecuta `resolve` para algunos `raw` representativos.
   - Si LLM sugiere categorías existentes, `CategoryMapper` guardará mappings automáticamente.
   - Si LLM sugiere nuevas categorías, las creará (y mapeará).
4. Revisa la tabla `category_mappings` y `categories` para confirmar resultados.
5. Corre `process_products` o `process_uncategorized` (si quieres procesar lotes) cuando estés satisfecho con la calidad.

## Problemas comunes
- LLM no disponible: los comandos LLM fallarán. Asegúrate de que Ollama corra y esté accesible.
- Respuestas LLM fuera de formato: la estrategia intenta extraer JSON del texto. Si tu LLM no respeta el formato, corrige el prompt o ajusta `parseResponse`.



# Zippy Commands — Category Mapping System

> **Package**: `boctulus/zippy`  
> **Namespace**: `Boctulus\Zippy\`  
> **Descripción**: Sistema de mapeo inteligente de categorías para productos usando LLM y matching difuso.

## Uso

```bash
php com zippy <namespace> <comando> [opciones]
```

## Namespace: `product`

### `product process`
Procesa productos y actualiza sus categorías.

```bash
php com zippy product process --limit=100 --dry-run
```

| Opción | Descripción |
|--------|-------------|
| `--limit=N` | Cantidad de productos (default: 100) |
| `--dry-run` | Modo simulación |
| `--strategy=X` | `llm` o `fuzzy` |

### `product batch`
Procesamiento batch optimizado para grandes volúmenes.

```bash
php com zippy product batch --limit=1000 --only-unmapped
```

| Opción | Descripción |
|--------|-------------|
| `--limit=N` | Cantidad de productos |
| `--offset=N` | Offset para paginación |
| `--only-unmapped` | Solo productos sin categorías |
| `--dry-run` | Modo simulación |

## Namespace: `category`

### Gestión

| Comando | Descripción |
|---------|-------------|
| `category all` | Lista todas las categorías |
| `category list_raw` | Lista categorías raw detectadas |
| `category create` | Crea nueva categoría |
| `category set` | Cambia padre de categoría |
| `category test` | Prueba reglas de matching |
| `category test_detailed` | Test detallado con similitud |
| `category resolve` | Resuelve categoría candidata |
| `category resolve_raw` | Resuelve desde texto raw |

### `category create`
```bash
php com zippy category create --name="Leche y derivados" --slug=dairy.milk --parent=dairy
```

## Namespace: `brand`

| Comando | Descripción |
|---------|-------------|
| `brand resolve` | Resuelve marca candidata desde texto |

## Namespace: `brand-categories`

| Comando | Descripción |
|---------|-------------|
| `brand-categories process` | Procesa categorías por marca |

## Namespace: `map-stats`

| Comando | Descripción |
|---------|-------------|
| `map-stats run` | Estadísticas de mapeo |

## Namespace: `review-mapping`

| Comando | Descripción |
|---------|-------------|
| `review-mapping run` | Revisión de mappings existentes |

## Namespace: `show-unmapped`

| Comando | Descripción |
|---------|-------------|
| `show-unmapped run` | Muestra productos sin mapear |

## Namespace: `weights`

| Comando | Descripción |
|---------|-------------|
| `weights adjust` | Ajusta pesos de matching |

## Namespace: `ollama`

| Comando | Descripción |
|---------|-------------|
| `ollama categories` | Genera categorías vía LLM local |

---

## Estrategias de Matching

| Estrategia | Descripción |
|------------|-------------|
| **LLM** | Usa Ollama (modelo local) para mapeo semántico |
| **Fuzzy** | Matching difuso por nombre/slug |

## Base de Datos

Requiere base `zippy` con tablas: `categories`, `category_mappings`, etc.

## Ver También

- [`packages/README.md`](./packages/README.md) — todos los packages
- [README del package](../packages/boctulus/zippy/README.md) — documentación completa

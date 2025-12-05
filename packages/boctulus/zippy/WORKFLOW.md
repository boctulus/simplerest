# Workflow de Clasificación de Productos Zippy

Este documento describe el flujo de trabajo recomendado para mantener y mejorar la clasificación automática de productos en el sistema Zippy.

## 1. Entrenamiento de la Red Neuronal

El sistema utiliza una red neuronal simple basada en pesos de palabras clave. A medida que más productos son categorizados correctamente (ya sea manualmente o automáticamente), el sistema puede aprender de ellos.

**Comando:**
```bash
php com zippy weights train --limit=5000
```

**Cuándo ejecutarlo:**
- Después de una carga masiva de productos categorizados.
- Periódicamente (ej: una vez por semana) si se están clasificando productos manualmente.
- Si notas que la clasificación automática está perdiendo precisión.

## 2. Clasificación Masiva de Productos

Para clasificar los productos que aún no tienen categoría asignada.

**Comando:**
```bash
# Procesar en lotes de 1000 productos sin categoría
php com zippy product batch --limit=1000 --only-unmapped
```

**Recomendación:**
- Ejecutar en lotes controlados (500-1000) para monitorear el progreso.
- Verificar los logs o la salida para detectar patrones de error.

## 3. Gestión de Categorías y Mapeos

El sistema puede sugerir categorías que no existen en la base de datos. Es importante revisar y crear estas categorías o crear alias (mappings).

### Detectar Categorías Faltantes
Ver qué categorías "raw" (detectadas en el texto) no están mapeadas a una categoría del sistema.

**Comando:**
```bash
php com zippy category list_raw --limit=100
```

### Crear Nuevas Categorías
Si una categoría detectada es válida y necesaria.

**Comando:**
```bash
# Ejemplo: Crear categoría "Sin TACC"
php com zippy category create --name="Sin TACC" --slug="dietetica.sintacc" --parent="dietetica"
```

### Crear Mapeos (Alias)
Si una categoría detectada corresponde a una existente (ej: "Gaseosas" -> "bebidas").

**Comando:**
```bash
# Ejemplo: Mapear "Gaseosas" a la categoría "bebidas"
php com zippy category create_mapping --slug="bebidas" --raw="Gaseosas"
```

## 4. Clasificación de Marcas

Asociar marcas a categorías ayuda significativamente a la clasificación de productos, ya que actúa como una señal fuerte.

**Comando:**
```bash
# Clasificar marcas automáticamente
php com zippy brand categorize
```

**Flujo Ideal:**
1.  Entrenar pesos (`weights train`).
2.  Clasificar marcas (`brand categorize`).
3.  Clasificar productos (`product batch`).
4.  Revisar categorías faltantes (`category list_raw`) y crear/mapear según sea necesario.
5.  Repetir.

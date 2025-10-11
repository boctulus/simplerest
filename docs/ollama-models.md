# Ollama Models Documentation

Esta documentación describe los modelos de IA instalados localmente mediante Ollama, incluyendo su propósito, alcance y tamaño.

## Modelos Instalados

### 1. Qwen 2.5 (1.5B)
**Modelo:** `qwen2.5:1.5b`
**Tamaño:** 986 MB
**Quantización:** Default

**Propósito:**
Modelo general ligero y rápido de Alibaba Cloud, optimizado para tareas de conversación y respuestas rápidas.

**Casos de Uso:**
- Respuestas rápidas a preguntas generales
- Conversaciones en tiempo real
- Tareas de procesamiento de lenguaje natural básicas
- Prototipado rápido con bajo consumo de recursos

**Características:**
- Muy rápido en inferencia
- Bajo consumo de memoria
- Multilenguaje (incluye español)
- Ideal para desarrollo y testing

---

### 2. DeepSeek-R1 (14B)
**Modelo:** `deepseek-r1:14b`
**Tamaño:** 9.0 GB
**Quantización:** Default

**Propósito:**
Modelo avanzado de razonamiento desarrollado por DeepSeek. Especializado en mostrar su proceso de pensamiento antes de dar respuestas.

**Casos de Uso:**
- Problemas que requieren razonamiento paso a paso
- Explicaciones detalladas de conceptos complejos
- Análisis lógico y resolución de problemas
- Matemáticas y ciencias
- Preguntas que requieren reflexión profunda

**Características:**
- Muestra su proceso de pensamiento con tags `<think>`
- Excelente para entender el razonamiento detrás de respuestas
- Alto nivel de precisión en tareas complejas
- Respuestas más largas y detalladas

**Nota:** Requiere más tiempo de procesamiento debido a la fase de "pensamiento".

---

### 3. DeepSeek-R1 (32B)
**Modelo:** `deepseek-r1:32b`
**Tamaño:** 19 GB
**Quantización:** Default

**Propósito:**
Versión más grande y potente del DeepSeek-R1, con capacidades de razonamiento aún más avanzadas.

**Casos de Uso:**
- Problemas extremadamente complejos
- Análisis profundo de código
- Razonamiento multi-paso avanzado
- Tareas que requieren máxima precisión
- Investigación y análisis académico

**Características:**
- Máxima capacidad de razonamiento
- Respuestas más precisas y completas que el modelo 14B
- Proceso de pensamiento más elaborado
- Mejor manejo de contextos largos

**Nota:** **⚠️ ADVERTENCIA - GPU Memory:** Este modelo puede causar errores `cudaMalloc failed: out of memory` en GPUs con memoria limitada. Úsalo solo si tienes suficiente VRAM disponible (>16GB recomendado).

---

### 4. Qwen 2.5 Coder (7B - Q4_K_M)
**Modelo:** `qwen2.5-coder:7b-instruct-q4_K_M`
**Tamaño:** 4.7 GB
**Quantización:** Q4_K_M (Balance óptimo calidad/tamaño)

**Propósito:**
Modelo especializado en programación y desarrollo de software. Versión cuantizada que mantiene excelente calidad con menor tamaño.

**Casos de Uso:**
- Generación de código en múltiples lenguajes
- Explicación y documentación de código
- Depuración y corrección de errores
- Refactorización de código
- Conversión entre lenguajes de programación
- Respuesta a preguntas técnicas sobre programación

**Lenguajes Soportados:**
Python, JavaScript, TypeScript, Java, C++, C#, Go, Rust, PHP, Ruby, Swift, Kotlin, y más.

**Características:**
- Excelente comprensión de contexto de código
- Genera código limpio y bien estructurado
- Entiende patrones de diseño y mejores prácticas
- Balance perfecto entre velocidad y calidad

**Recomendación:** **⭐ MODELO RECOMENDADO PARA DESARROLLO**

---

### 5. DeepSeek Coder (6.7B - Q4_K_M)
**Modelo:** `deepseek-coder:6.7b-instruct-q4_K_M`
**Tamaño:** 4.1 GB
**Quantización:** Q4_K_M

**Propósito:**
Modelo de DeepSeek especializado exclusivamente en tareas de programación.

**Casos de Uso:**
- Generación de código complejo
- Análisis de código existente
- Optimización de algoritmos
- Debugging avanzado
- Code review automatizado
- Generación de tests unitarios

**Lenguajes Soportados:**
Excelente soporte para: Python, JavaScript, Java, C++, Go, Rust, SQL

**Características:**
- Comprensión profunda de lógica de programación
- Excelente para algoritmos y estructuras de datos
- Genera tests unitarios de alta calidad
- Muy bueno para explicar código complejo

---

### 6. CodeLLaMA (13B - Q4_K_M)
**Modelo:** `codellama:13b-instruct-q4_K_M`
**Tamaño:** 7.9 GB
**Quantización:** Q4_K_M

**Propósito:**
Modelo de Meta especializado en código, basado en LLaMA 2. Versión más grande con mayor capacidad.

**Casos de Uso:**
- Proyectos de código grandes y complejos
- Arquitectura de software
- Diseño de sistemas
- Code completion avanzado
- Refactorización a gran escala
- Generación de documentación técnica

**Lenguajes Soportados:**
Python, Java, JavaScript, C++, C#, TypeScript, PHP, y más.

**Características:**
- Mayor contexto que modelos más pequeños
- Excelente para proyectos enterprise
- Bueno para entender bases de código grandes
- Puede manejar múltiples archivos relacionados

**Nota:** Requiere más memoria y tiempo de procesamiento, pero ofrece respuestas más completas.

---

## Guía de Selección de Modelo

### Para Desarrollo Rápido y Testing:
- **qwen2.5:1.5b** - Respuestas rápidas, bajo consumo

### Para Programación General:
- **qwen2.5-coder:7b-instruct-q4_K_M** ⭐ - Mejor balance calidad/velocidad
- **deepseek-coder:6.7b-instruct-q4_K_M** - Alternativa excelente

### Para Proyectos Complejos:
- **codellama:13b-instruct-q4_K_M** - Máxima capacidad de código

### Para Razonamiento y Explicaciones:
- **deepseek-r1:14b** - Razonamiento paso a paso
- **deepseek-r1:32b** - Máxima capacidad (⚠️ requiere GPU potente)

---

## Comparación de Tamaños

| Modelo | Tamaño | Velocidad | Calidad | GPU Memory |
|--------|---------|-----------|---------|------------|
| qwen2.5:1.5b | 986 MB | ⚡⚡⚡⚡⚡ | ⭐⭐⭐ | ~2 GB |
| qwen2.5-coder:7b (Q4) | 4.7 GB | ⚡⚡⚡⚡ | ⭐⭐⭐⭐ | ~6 GB |
| deepseek-coder:6.7b (Q4) | 4.1 GB | ⚡⚡⚡⚡ | ⭐⭐⭐⭐ | ~5 GB |
| codellama:13b (Q4) | 7.9 GB | ⚡⚡⚡ | ⭐⭐⭐⭐⭐ | ~10 GB |
| deepseek-r1:14b | 9.0 GB | ⚡⚡ | ⭐⭐⭐⭐⭐ | ~12 GB |
| deepseek-r1:32b | 19 GB | ⚡ | ⭐⭐⭐⭐⭐ | ~24 GB |

---

## Uso con la API

### Ejemplo básico:
```bash
# Listar modelos disponibles
php com llm ollama:list

# Usar un modelo específico
php com llm ollama:prompt "Tu pregunta aquí" "qwen2.5-coder:7b-instruct-q4_K_M"
```

### Desde PHP:
```php
use Boctulus\LLMProviders\Factory\LLMFactory;

$llm = LLMFactory::ollama();

$llm->setModel('qwen2.5-coder:7b-instruct-q4_K_M')
    ->addContent('Escribe una función para validar un email en PHP');

$response = $llm->exec();

if ($response['status'] == 200) {
    echo $llm->getContent();
}
```

---

## Quantización Explicada

Los modelos con sufijo **Q4_K_M** usan quantización de 4-bit con método K-means mixto:

- **Ventajas:**
  - ~75% reducción de tamaño vs. modelos completos
  - 3-4x más rápido en inferencia
  - Usa mucha menos memoria GPU/RAM

- **Desventajas:**
  - Pérdida mínima de calidad (~2-5%)
  - En la mayoría de casos, la diferencia es imperceptible

**Otros niveles de quantización:**
- **Q2**: Máxima compresión, calidad reducida
- **Q3**: Buen balance para modelos muy grandes
- **Q4_K_M**: ⭐ Recomendado - mejor balance calidad/tamaño
- **Q5_K_M**: Mayor calidad, mayor tamaño
- **Q6_K**: Calidad casi completa
- **Q8_0**: Máxima calidad, tamaño mayor

---

## Notas Técnicas

### Configuración Actual:
- **Servidor:** Ollama corriendo en `http://localhost:11434`
- **Streaming:** Deshabilitado (`stream: false`) para respuestas completas
- **Timeout:** Configurado según tamaño del modelo

### Recomendaciones:
1. Para desarrollo diario: usa **qwen2.5-coder:7b-instruct-q4_K_M**
2. Para máxima velocidad: usa **qwen2.5:1.5b**
3. Para máxima calidad en código: usa **codellama:13b-instruct-q4_K_M**
4. Evita **deepseek-r1:32b** si tienes <16GB VRAM

### Mantenimiento:
```bash
# Ver modelos instalados
ollama list

# Eliminar un modelo
ollama rm nombre-modelo

# Actualizar un modelo
ollama pull nombre-modelo

# Ver uso de recursos
ollama ps
```

---

**Última actualización:** 2025-10-11

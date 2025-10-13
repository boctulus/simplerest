# Documentación: Lógica Condicional del Typeform

## 🧠 Cómo Funciona la Lógica Condicional

### **Sistema de step-alias y Condicionales**

El sistema permite mostrar u ocultar pasos basándose en las respuestas del usuario usando dos atributos:

1. **`data-step-alias`**: Identificador único del paso (ej: `"electronic-signature"`)
2. **`data-conditional`**: Regla que determina cuándo mostrar el paso (ej: `"has_signature:no"`)

### **Formato de Reglas Condicionales**

```html
<div data-step-alias="nombre-paso" data-conditional="campo:valor">
```

#### **Tipos de Reglas:**

1. **Igualdad**: `"campo:valor"` → Se muestra si `formData.campo === "valor"`
2. **Negación**: `"campo:!valor"` → Se muestra si `formData.campo !== "valor"`

### **Ejemplo Práctico: Flujo de Firma Electrónica**

```html
<!-- Paso 6: Pregunta sobre firma electrónica -->
<div data-step="6" data-step-alias="electronic-signature">
    <input type="radio" name="has_signature" value="yes"> Sí, tengo firma
    <input type="radio" name="has_signature" value="no"> No, necesito obtenerla
</div>

<!-- Paso 10: Solo aparece si NO tiene firma -->
<div data-step="10" data-step-alias="review-submit" data-conditional="has_signature:no">
    <h2>Revisión Final</h2>
    <!-- Este paso solo aparece si has_signature = "no" -->
</div>

<!-- Paso 11: Solo aparece si NO tiene firma -->
<div data-step="11" data-step-alias="thank-you" data-conditional="has_signature:no">
    <h2>¡Gracias!</h2>
    <!-- Este paso solo aparece si has_signature = "no" -->
</div>
```

## 🔄 Flujos de Navegación

### **Escenario 1: Usuario CON firma electrónica**
```
Usuario selecciona "Sí, tengo firma electrónica" (has_signature = "yes")

Flujo: 1 → 2 → 3 → 4 → 5 → 6 → 8 → 9 → 10 → 11
       ↑                   ↑   ↑   ↑    ↑    ↑
   Welcome            Firma  Upload    Review Thank You
                      
❌ Paso 7 se OCULTA porque has_signature ≠ "no"
✅ Navegación salta automáticamente del 6 al 8
```

### **Escenario 2: Usuario SIN firma electrónica**
```
Usuario selecciona "No. Necesito obtenerla" (has_signature = "no")

Flujo: 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9 → 10 → 11
       ↑                   ↑   ↑   ↑   ↑    ↑    ↑
   Welcome            Firma SII Upload    Review Thank You
                      
✅ Paso 7 se MUESTRA porque has_signature = "no"
✅ Navegación incluye el paso 7 (aviso SII)
```

### **🎯 Momento Crítico de Evaluación**
```
PASO 6: Usuario hace selección de firma electrónica
   ↓
🔄 Al hacer clic en "Continuar":
   1. Se recolectan datos del formulario (incluyendo has_signature)
   2. Se evalúan TODAS las condiciones inmediatamente
   3. Se ocultan/muestran pasos según corresponda
   4. Se determina cuál es el SIGUIENTE paso visible
   5. Se navega al paso correcto
```

## ⚙️ Implementación Técnica

### **1. Detección de Cambios**
```javascript
// Se actualiza la visibilidad cuando cambia cualquier input
document.addEventListener('change', () => {
    ConditionalSteps.updateStepVisibility();
});
```

### **2. Navegación Inteligente**
```javascript
// nextStep() modificado para saltar pasos ocultos
static getNextVisibleStep(currentStep, formData) {
    const allStepNumbers = [1, 2, 3, 4, 5, 6, 8, 10, 11]; // Números reales
    
    for (const stepNumber of allStepNumbers) {
        if (stepNumber > currentStep) {
            const alias = this.getAliasByStep(stepNumber);
            if (!alias || this.shouldShowStep(alias, formData)) {
                return stepNumber; // Este paso es visible
            }
        }
    }
    return null; // No hay más pasos
}
```

### **3. Evaluación de Condiciones**
```javascript
static shouldShowStep(stepAlias, formData) {
    const stepData = this.stepMap.get(stepAlias);
    if (!stepData.conditional) return true; // Sin condición = siempre visible
    
    const [field, expectedValue] = stepData.conditional.split(':');
    const actualValue = formData[field];
    
    if (expectedValue.startsWith('!')) {
        // Negación: mostrar si NO es igual
        return actualValue !== expectedValue.substring(1);
    } else {
        // Igualdad: mostrar si ES igual
        return actualValue === expectedValue;
    }
}
```

## 🐛 Debugging y Troubleshooting

### **Comandos de Debug en Consola:**

```javascript
// Ver TODOS los pasos y su estado
allStepsStatus()

// Ver estado completo de pasos condicionales
ConditionalSteps.debug();

// Ver datos actuales del formulario
DataPersistence.collectFormData();

// Forzar actualización de visibilidad
ConditionalSteps.updateStepVisibility();
```

### **Problemas Comunes:**

1. **"Pantalla en blanco al navegar"**
   - Causa: Navegación a paso que no existe o está oculto
   - Solución: Verificar que `getNextVisibleStep()` encuentra pasos válidos

2. **"Pasos no se ocultan/muestran"**
   - Causa: `data-conditional` mal formateado o nombre de campo incorrecto
   - Solución: Verificar que el nombre del campo coincida exactamente

3. **"Navegación se 'atasca'"**
   - Causa: Todos los siguientes pasos están ocultos
   - Solución: Usar botones flotantes de retroceso/reinicio

## 🎛️ Botones Flotantes de Ayuda

### **Funcionalidades:**

1. **🔄 Botón Reinicio (Rojo)**
   - Limpia todos los datos almacenados
   - Recarga la página
   - Vuelve al paso 1

2. **⬅️ Botón Retroceso (Gris)**
   - Navega al paso anterior visible
   - Se deshabilita en el paso 1
   - Respeta la lógica condicional

### **Uso en Emergencias:**
- Si te quedas "atascado" en un paso sin opciones de navegación
- Para probar diferentes flujos rápidamente
- Para volver atrás sin perder datos hasta confirmar el reinicio

## 📋 Resumen de Pasos del Formulario

| Paso | Archivo | Alias | Condicional | Descripción |
|------|---------|-------|-------------|-------------|
| 1 | step-1-welcome.php | welcome | - | Bienvenida |
| 2 | step-2-document-types.php | - | - | Tipos de documentos |
| 3 | step-3-business-info.php | business-info | - | Info empresa |
| 4 | step-3b-banking-info.php | - | - | Datos bancarios |
| 5 | step-4-legal-representative.php | legal-representative | - | Representante legal |
| 6 | step-5-electronic-signature.php | electronic-signature | - | **Punto de decisión** |
| 8 | step-6-upload-documents.php | - | - | Subir documentos |
| 10 | step-7-review-submit.php | review-submit | `has_signature:no` | ⚡ **Condicional** |
| 11 | step-8-thank-you.php | thank-you | `has_signature:no` | ⚡ **Condicional** |

### **Flujo Visual:**
```
[1] → [2] → [3] → [4] → [5] → [6] → [8] ─┐
                              ↓          │
                         ¿Tiene firma?   │
                              ↓          │
                           Si [YES] ─────┘ (FIN)
                              ↓
                           No [NO]
                              ↓
                           [10] → [11] (FIN)
```

## 🚀 Extensibilidad

### **Para Agregar Nuevos Flujos Condicionales:**

1. **Agregar data-conditional a la vista:**
   ```html
   <div data-step-alias="nuevo-paso" data-conditional="campo:valor">
   ```

2. **El sistema detectará automáticamente el nuevo paso**
   - No requiere modificar JavaScript
   - Se integrará automáticamente en la navegación

3. **Condiciones más complejas (futuro):**
   ```html
   data-conditional="campo1:valor1,campo2:!valor2"  <!-- AND lógico -->
   data-conditional="campo1:valor1|campo2:valor2"   <!-- OR lógico -->
   ```

El sistema está diseñado para ser extensible y mantenible sin requirir cambios profundos al código JavaScript base.
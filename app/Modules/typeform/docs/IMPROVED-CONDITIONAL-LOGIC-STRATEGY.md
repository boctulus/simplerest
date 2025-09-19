# Estrategia Mejorada: Lógica Condicional Robusta

## 🎯 **PROBLEMAS DE LA ESTRATEGIA ACTUAL**

### **Problemas Críticos Identificados:**

1. **Navegación Frágil**: Array hardcodeado de números de pasos
2. **Documentación Desactualizada**: Condiciones incorrectas en docs
3. **Falta de Fallbacks**: Sin recuperación cuando falla la navegación  
4. **Validación Inconsistente**: Mezclada entre DOM y lógica
5. **No Escalable**: Agregar pasos requiere tocar múltiples archivos

## 🚀 **NUEVA ESTRATEGIA: "NAVIGATION GRAPH"**

### **Concepto Central: Grafo de Navegación Dinámico**

En lugar de depender de números de pasos, crear un **grafo de navegación** que se construye dinámicamente desde el DOM.

```javascript
class NavigationGraph {
    constructor() {
        this.nodes = new Map(); // stepAlias -> StepNode
        this.edges = new Map(); // stepAlias -> [nextSteps]
        this.buildFromDOM();
    }
    
    buildFromDOM() {
        // Construir nodos y edges automáticamente desde el DOM
        const steps = document.querySelectorAll('[data-step]');
        
        for (let step of steps) {
            const stepNumber = parseInt(step.getAttribute('data-step'));
            const stepAlias = step.getAttribute('data-step-alias') || `step-${stepNumber}`;
            const conditional = step.getAttribute('data-conditional');
            
            this.nodes.set(stepAlias, {
                stepNumber,
                element: step,
                conditional,
                isVisible: () => this.evaluateCondition(conditional)
            });
        }
        
        // Construir edges (conexiones) basado en orden numérico
        const sortedSteps = Array.from(this.nodes.values())
            .sort((a, b) => a.stepNumber - b.stepNumber);
            
        for (let i = 0; i < sortedSteps.length - 1; i++) {
            const current = sortedSteps[i];
            const next = sortedSteps[i + 1];
            
            if (!this.edges.has(current.alias)) {
                this.edges.set(current.alias, []);
            }
            this.edges.get(current.alias).push(next.alias);
        }
    }
}
```

### **Ventajas del Navigation Graph:**

1. **🔄 Auto-construido**: Se genera automáticamente desde el DOM
2. **📈 Escalable**: Agregar pasos solo requiere HTML
3. **🛡️ Robusto**: No depende de números hardcodeados
4. **🔍 Debuggeable**: Estado completo visible en cualquier momento
5. **⚡ Eficiente**: Navegación O(1) en lugar de búsqueda lineal

## 🏗️ **ARQUITECTURA MEJORADA**

### **1. StepManager Mejorado**

```javascript
class ImprovedStepManager {
    constructor() {
        this.navigationGraph = new NavigationGraph();
        this.currentStepAlias = 'welcome';
        this.validationEngine = new ValidationEngine();
        this.recoveryManager = new RecoveryManager();
    }
    
    async navigateNext() {
        try {
            // 1. Validar paso actual
            const isValid = await this.validationEngine.validateStep(this.currentStepAlias);
            if (!isValid) {
                this.showValidationErrors();
                return false;
            }
            
            // 2. Encontrar siguiente paso visible
            const nextStep = this.navigationGraph.getNextVisibleStep(this.currentStepAlias);
            if (!nextStep) {
                // 3. Manejo de error con recuperación
                return this.recoveryManager.handleEndOfFlow();
            }
            
            // 4. Navegar
            return this.showStep(nextStep);
            
        } catch (error) {
            // 5. Recuperación de errores
            return this.recoveryManager.handleNavigationError(error);
        }
    }
}
```

### **2. Validación Desacoplada**

```javascript
class ValidationEngine {
    constructor() {
        this.validators = new Map();
        this.registerDefaultValidators();
    }
    
    async validateStep(stepAlias) {
        const stepNode = this.navigationGraph.nodes.get(stepAlias);
        if (!stepNode) return true;
        
        // Validación DOM estándar
        const domValid = this.validateRequiredFields(stepNode.element);
        
        // Validación custom por step
        const customValidator = this.validators.get(stepAlias);
        const customValid = customValidator ? await customValidator(stepNode) : true;
        
        return domValid && customValid;
    }
    
    registerValidator(stepAlias, validatorFn) {
        this.validators.set(stepAlias, validatorFn);
    }
}
```

### **3. Sistema de Recuperación**

```javascript
class RecoveryManager {
    handleNavigationError(error) {
        console.error('Navigation error:', error);
        
        // Estrategias de recuperación en orden de preferencia:
        return this.tryRecoveryStrategies([
            () => this.navigateToSafeStep(),
            () => this.showEmergencyControls(),
            () => this.reloadForm()
        ]);
    }
    
    navigateToSafeStep() {
        // Ir al último paso válido conocido
        const safeSteps = ['welcome', 'review-submit'];
        for (let stepAlias of safeSteps) {
            if (this.navigationGraph.nodes.has(stepAlias)) {
                return this.stepManager.showStep(stepAlias);
            }
        }
        return false;
    }
}
```

## 📊 **FLUJOS DE NAVEGACIÓN DECLARATIVOS**

### **Definición por Configuración:**

```javascript
const FLOW_DEFINITIONS = {
    'document-selection': {
        'cards-only': {
            path: ['welcome', 'document-types', 'business-info', 'banking-info', 'legal-representative', 'review-submit', 'thank-you'],
            conditions: { 'document_types[]': (val) => val.includes('cards') && val.length === 1 }
        },
        'documents-with-signature': {
            path: ['welcome', 'document-types', 'business-info', 'banking-info', 'legal-representative', 'electronic-signature', 'upload-signature', 'review-submit', 'thank-you'],
            conditions: { 
                'document_types[]': (val) => val.some(type => ['invoices', 'receipts'].includes(type)),
                'has_signature': 'yes'
            }
        },
        'documents-without-signature': {
            path: ['welcome', 'document-types', 'business-info', 'banking-info', 'legal-representative', 'electronic-signature', 'sii-notice', 'upload-documents', 'review-submit', 'thank-you'],
            conditions: { 
                'document_types[]': (val) => val.some(type => ['invoices', 'receipts'].includes(type)),
                'has_signature': 'no'
            }
        }
    }
};
```

## 🔧 **IMPLEMENTACIÓN PRÁCTICA**

### **Paso 1: NavigationGraph Básico**

```javascript
// 1. Reemplazar getNextVisibleStep() hardcodeado
static getNextVisibleStep(currentStepAlias) {
    const currentNode = this.navigationGraph.nodes.get(currentStepAlias);
    if (!currentNode) return null;
    
    // Obtener todos los pasos siguientes ordenados
    const allSteps = Array.from(this.navigationGraph.nodes.values())
        .filter(node => node.stepNumber > currentNode.stepNumber)
        .sort((a, b) => a.stepNumber - b.stepNumber);
    
    // Encontrar el primero que sea visible
    for (let step of allSteps) {
        if (step.isVisible()) {
            return step.stepAlias;
        }
    }
    
    return null; // No hay más pasos
}
```

### **Paso 2: Validación Mejorada**

```javascript
// 2. Validación que incluye recuperación
validateCurrentStep() {
    try {
        const result = this.validationEngine.validateStep(this.currentStepAlias);
        if (!result.isValid) {
            // Mostrar errores específicos en lugar de fallar silenciosamente
            this.showValidationErrors(result.errors);
            return false;
        }
        return true;
    } catch (error) {
        // Log del error pero no bloquear completamente
        console.error('Validation error:', error);
        return this.recoveryManager.handleValidationError(error);
    }
}
```

### **Paso 3: Debug y Monitoreo**

```javascript
// 3. Sistema de debug robusto
class NavigationDebugger {
    static logNavigationAttempt(from, to, reason) {
        console.group(`🧭 Navigation: ${from} → ${to || 'BLOCKED'}`);
        console.log('Reason:', reason);
        console.log('Form data:', this.collectFormData());
        console.log('Visible steps:', this.getVisibleSteps());
        console.groupEnd();
    }
    
    static getVisibleSteps() {
        return Array.from(this.navigationGraph.nodes.entries())
            .filter(([alias, node]) => node.isVisible())
            .map(([alias, node]) => `${alias} (${node.stepNumber})`);
    }
}
```

## 🎯 **BENEFICIOS DE LA NUEVA ESTRATEGIA**

### **Inmediatos:**
- ✅ **Navegación Robusta**: Nunca más pantallas en blanco
- ✅ **Debug Claro**: Estado completo visible siempre
- ✅ **Recuperación Automática**: Fallbacks cuando algo falla
- ✅ **Validación Clara**: Errores específicos mostrados al usuario

### **A Largo Plazo:**
- 🔄 **Escalabilidad**: Agregar pasos solo requiere HTML
- 📈 **Mantenibilidad**: Un solo lugar para lógica de navegación  
- 🛡️ **Estabilidad**: Resistente a cambios y errores
- 🔍 **Observabilidad**: Métricas y logging completos

## 📋 **PLAN DE IMPLEMENTACIÓN**

### **Fase 1: Fundación (1-2 días)**
1. Crear `NavigationGraph` básico
2. Reemplazar `getNextVisibleStep()` hardcodeado
3. Agregar logging de debug

### **Fase 2: Validación (1 día)**  
1. Crear `ValidationEngine` desacoplado
2. Mejorar mensajes de error
3. Agregar fallbacks de validación

### **Fase 3: Recuperación (1 día)**
1. Implementar `RecoveryManager`
2. Agregar botones de emergencia mejorados
3. Testing completo de casos edge

### **Fase 4: Optimización (1 día)**
1. Flujos declarativos por configuración
2. Métricas y analytics
3. Documentación actualizada

## 🚀 **RESULTADO ESPERADO**

Un sistema de navegación condicional que:
- **Nunca falla silenciosamente**
- **Siempre puede recuperarse de errores**
- **Es fácil de debuggear y mantener**  
- **Escala sin requerir cambios de código**
- **Proporciona UX consistente y confiable**

Esta estrategia convierte un sistema frágil y difícil de mantener en una solución robusta y escalable que elimina definitivamente el problema de "pantallas sin pasos visibles".
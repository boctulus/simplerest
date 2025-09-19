/**
 * Floating Controls for Typeform
 * Provides restart and back buttons for navigation assistance
 */
class FloatingControls {
    
    /**
     * Initialize floating controls
     */
    static init() {
        this.createFloatingControls();
        this.attachEventListeners();
        console.log('✅ FloatingControls initialized');
    }
    
    /**
     * Create floating control buttons
     */
    static createFloatingControls() {
        // Create container
        const container = document.createElement('div');
        container.id = 'floating-controls';
        container.style.cssText = `
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        
        // Create restart button
        const restartButton = document.createElement('button');
        restartButton.id = 'floating-restart';
        restartButton.type = 'button';
        restartButton.innerHTML = '🔄';
        restartButton.title = 'Reiniciar formulario';
        restartButton.style.cssText = `
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: #dc3545;
            color: white;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        // Create back button
        const backButton = document.createElement('button');
        backButton.id = 'floating-back';
        backButton.type = 'button';
        backButton.innerHTML = '⬅️';
        backButton.title = 'Retroceder al paso anterior';
        backButton.style.cssText = `
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: #6c757d;
            color: white;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        // Add hover effects
        this.addHoverEffects(restartButton, '#c82333');
        this.addHoverEffects(backButton, '#5a6268');
        
        // Append to container
        container.appendChild(restartButton);
        container.appendChild(backButton);
        
        // Append to body
        document.body.appendChild(container);
        
        this.container = container;
        this.restartButton = restartButton;
        this.backButton = backButton;
    }
    
    /**
     * Add hover effects to a button
     * @param {HTMLElement} button - The button element
     * @param {string} hoverColor - The hover background color
     */
    static addHoverEffects(button, hoverColor) {
        const originalBg = button.style.background;
        
        button.addEventListener('mouseenter', () => {
            button.style.background = hoverColor;
            button.style.transform = 'scale(1.1)';
        });
        
        button.addEventListener('mouseleave', () => {
            button.style.background = originalBg;
            button.style.transform = 'scale(1)';
        });
    }
    
    /**
     * Attach event listeners to floating controls
     */
    static attachEventListeners() {
        // Restart button
        this.restartButton.addEventListener('click', () => {
            this.handleRestart();
        });
        
        // Back button
        this.backButton.addEventListener('click', () => {
            this.handleBack();
        });
        
        // Update back button visibility based on current step
        this.updateBackButtonVisibility();
        
        // Listen for step changes to update button states
        document.addEventListener('stepChanged', () => {
            this.updateBackButtonVisibility();
        });
    }
    
    /**
     * Handle restart action
     */
    static handleRestart() {
        // Show confirmation dialog
        const confirmed = confirm('¿Estás seguro de que quieres reiniciar el formulario? Se perderán todos los datos ingresados.');
        
        if (confirmed) {
            console.log('🔄 User confirmed restart');
            
            // Clear all form data
            if (window.DataPersistence) {
                window.DataPersistence.clearData();
            }
            
            // Show success message
            this.showMessage('🔄 Formulario reiniciado', '#28a745');
            
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }
    
    /**
     * Handle back navigation
     */
    static handleBack() {
        if (!window.stepManager) {
            console.warn('StepManager not available for back navigation');
            this.showMessage('❌ Sistema de navegación no disponible', '#dc3545');
            return;
        }
        
        const currentStep = window.stepManager.currentStep;
        console.log(`⬅️ Floating back button clicked from step ${currentStep}`);
        
        // Use the enhanced prevStep function if available
        if (typeof window.prevStep === 'function') {
            console.log('✅ Using enhanced prevStep function');
            window.prevStep();
        } else if (window.ConditionalSteps && typeof window.ConditionalSteps.getPrevVisibleStep === 'function') {
            // Use ConditionalSteps directly if prevStep is not available
            console.log('✅ Using ConditionalSteps.getPrevVisibleStep directly');
            const formData = window.DataPersistence ? window.DataPersistence.collectFormData() : null;
            const prevVisibleStep = window.ConditionalSteps.getPrevVisibleStep(currentStep, formData);
            
            if (prevVisibleStep) {
                window.stepManager.showStep(prevVisibleStep);
                console.log(`⬅️ Navigated to step ${prevVisibleStep}`);
            } else {
                console.log('❌ No previous visible step found');
                this.showMessage('❌ No hay pasos anteriores disponibles', '#dc3545');
            }
        } else {
            // Fallback to simple navigation (non-conditional)
            console.log('⚠️ Using fallback navigation');
            const prevStep = Math.max(1, currentStep - 1);
            if (prevStep < currentStep) {
                window.stepManager.showStep(prevStep);
                console.log(`⬅️ Fallback navigation to step ${prevStep}`);
            } else {
                this.showMessage('❌ Ya estás en el primer paso', '#dc3545');
            }
        }
    }
    
    /**
     * Update back button visibility based on current step
     */
    static updateBackButtonVisibility() {
        if (!window.stepManager) return;
        
        const currentStep = window.stepManager.currentStep;
        
        if (currentStep <= 1) {
            this.backButton.style.opacity = '0.5';
            this.backButton.style.cursor = 'not-allowed';
            this.backButton.disabled = true;
        } else {
            this.backButton.style.opacity = '1';
            this.backButton.style.cursor = 'pointer';
            this.backButton.disabled = false;
        }
    }
    
    /**
     * Show a temporary message
     * @param {string} message - The message to show
     * @param {string} color - The background color
     */
    static showMessage(message, color = '#007cba') {
        const messageEl = document.createElement('div');
        messageEl.textContent = message;
        messageEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${color};
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            z-index: 10001;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        `;
        
        // Add animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(messageEl);
        
        // Remove after 3 seconds
        setTimeout(() => {
            messageEl.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => messageEl.remove(), 300);
        }, 3000);
    }
    
    /**
     * Hide floating controls (for specific scenarios)
     */
    static hide() {
        if (this.container) {
            this.container.style.display = 'none';
        }
    }
    
    /**
     * Show floating controls
     */
    static show() {
        if (this.container) {
            this.container.style.display = 'flex';
        }
    }
    
    /**
     * Debug floating controls state
     */
    static debug() {
        console.log('🔍 FloatingControls Debug:');
        console.log('- Container exists:', !!this.container);
        console.log('- Restart button exists:', !!this.restartButton);
        console.log('- Back button exists:', !!this.backButton);
        console.log('- Current step:', window.stepManager ? window.stepManager.currentStep : 'N/A');
        console.log('- Back button enabled:', !this.backButton?.disabled);
        console.log('- window.prevStep exists:', typeof window.prevStep);
        console.log('- window.nextStep exists:', typeof window.nextStep);
        console.log('- window.ConditionalSteps exists:', !!window.ConditionalSteps);
        console.log('- window.typeformController exists:', !!window.typeformController);
        
        if (window.stepManager) {
            console.log('- StepManager currentStep:', window.stepManager.currentStep);
        }
        
        if (window.ConditionalSteps) {
            console.log('- ConditionalSteps.stepMap size:', window.ConditionalSteps.stepMap?.size || 0);
        }
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    FloatingControls.init();
});

// Listen for step manager initialization
const checkStepManager = () => {
    if (window.stepManager) {
        // Hook into step manager events
        const originalShowStep = window.stepManager.showStep;
        if (originalShowStep) {
            window.stepManager.showStep = function(step) {
                const result = originalShowStep.call(this, step);
                
                // Dispatch custom event for floating controls
                document.dispatchEvent(new CustomEvent('stepChanged', { detail: { step } }));
                
                return result;
            };
        }
    } else {
        setTimeout(checkStepManager, 100);
    }
};

checkStepManager();
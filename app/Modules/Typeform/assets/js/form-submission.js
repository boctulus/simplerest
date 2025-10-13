/**
 * Form submission handling
 */
class FormSubmission {
    constructor(stepManager) {
        this.stepManager = stepManager;
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.stepManager.validateCurrentStep()) return;
        
        // Show loading state
        const submitBtn = document.querySelector('.btn-submit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando... <span class="loading-spinner">⏳</span>';
        }

        try {
            // Prepare form data for submission
            const formData = new FormData();
            const data = this.stepManager.getFormData();
            
            Object.keys(data).forEach(key => {
                if (Array.isArray(data[key])) {
                    data[key].forEach(value => {
                        formData.append(key, value);
                    });
                } else {
                    formData.append(key, data[key]);
                }
            });

            // Add files
            document.querySelectorAll('input[type="file"]').forEach(fileInput => {
                if (fileInput.files[0]) {
                    formData.append(fileInput.name, fileInput.files[0]);
                }
            });

            // Submit to server
            const response = await fetch('/typeform/process', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Clear stored data
                DataPersistence.clearData();
                
                // Show success step
                this.stepManager.showStep(8);
            } else {
                FormHandlers.showError('Error al enviar el formulario. Por favor intenta nuevamente.');
            }
        } catch (error) {
            console.error('Form submission error:', error);
            FormHandlers.showError('Error de conexión. Por favor verifica tu conexión a internet.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Enviar Solicitud <span class="btn-arrow">✓</span>';
            }
        }
    }
}
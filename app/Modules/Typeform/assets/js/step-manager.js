/**
 * Step management for multi-step form
 */
class StepManager {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 7; // Excluding thank you step
    }

    updateProgressBar() {
        const progress = (this.currentStep / this.totalSteps) * 100;
        const progressFill = document.getElementById('progressFill');
        if (progressFill) {
            progressFill.style.width = progress + '%';
        }
    }

    showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
        
        // Show target step
        const targetStep = document.querySelector(`[data-step="${step}"]`);
        if (targetStep) {
            targetStep.classList.add('active');
        }

        // Update hidden input
        const currentStepInput = document.getElementById('currentStep');
        if (currentStepInput) {
            currentStepInput.value = step;
        }

        this.currentStep = step;
        this.updateProgressBar();

        // Update form summary if on review step
        if (step === 7) {
            this.updateFormSummary();
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    nextStep() {
        if (this.validateCurrentStep()) {
            if (this.currentStep < 8) {
                this.showStep(this.currentStep + 1);
            }
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.showStep(this.currentStep - 1);
        }
    }

    validateCurrentStep() {
        const current = document.querySelector(`[data-step="${this.currentStep}"]`);
        if (!current) return true;

        const requiredFields = current.querySelectorAll('[required]');
        let isValid = true;

        for (let field of requiredFields) {
            if (!FormValidation.validateField(field)) {
                isValid = false;
                if (field.offsetParent !== null) { // Check if visible
                    field.focus();
                    break;
                }
            }
        }

        // Special validations
        if (this.currentStep === 2) {
            const documentTypes = current.querySelectorAll('input[name="document_types[]"]:checked');
            if (documentTypes.length === 0) {
                FormHandlers.showError('Por favor selecciona al menos un tipo de documento');
                isValid = false;
            }
        }

        if (this.currentStep === 5) {
            const hasSignature = current.querySelector('input[name="has_signature"]:checked');
            if (!hasSignature) {
                FormHandlers.showError('Por favor selecciona una opción para la firma electrónica');
                isValid = false;
            }
        }

        return isValid;
    }

    updateFormSummary() {
        const summaryContainer = document.getElementById('form-summary');
        if (!summaryContainer) return;

        const formData = this.getFormData();
        let summaryHTML = '';

        // Document types
        const docTypes = formData['document_types[]'] || [];
        if (docTypes.length > 0) {
            summaryHTML += `<p><strong>Tipos de documentos:</strong> ${docTypes.join(', ')}</p>`;
        }

        // Business info
        if (formData.business_name) {
            summaryHTML += `<p><strong>Empresa:</strong> ${formData.business_name}</p>`;
        }
        if (formData.rut) {
            summaryHTML += `<p><strong>RUT:</strong> ${formData.rut}</p>`;
        }

        // Legal representative
        if (formData.legal_rep_name) {
            summaryHTML += `<p><strong>Representante Legal:</strong> ${formData.legal_rep_name}</p>`;
        }
        if (formData.legal_rep_email) {
            summaryHTML += `<p><strong>Email:</strong> ${formData.legal_rep_email}</p>`;
        }

        // Electronic signature
        if (formData.has_signature) {
            const hasSignText = formData.has_signature === 'yes' ? 'Sí tiene' : 'No tiene';
            summaryHTML += `<p><strong>Firma electrónica:</strong> ${hasSignText}</p>`;
        }

        summaryContainer.innerHTML = summaryHTML || '<p>No hay datos para mostrar</p>';
    }

    getFormData() {
        const formData = {};
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                if (input.checked) {
                    if (input.name.includes('[]')) {
                        if (!formData[input.name]) formData[input.name] = [];
                        formData[input.name].push(input.value);
                    } else {
                        formData[input.name] = input.value;
                    }
                }
            } else if (input.type !== 'file' && input.name) {
                formData[input.name] = input.value;
            }
        });

        return formData;
    }
}
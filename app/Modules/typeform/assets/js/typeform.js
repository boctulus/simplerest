/**
 * Main Typeform controller - orchestrates all modules
 */
class TypeformController {
    constructor() {
        this.stepManager = new StepManager();
        this.formSubmission = new FormSubmission(this.stepManager);
        this.init();
    }

    init() {
        this.stepManager.updateProgressBar();
        FormHandlers.bindEvents();
        this.loadStoredData();
        this.bindFormSubmit();
    }

    bindFormSubmit() {
        const form = document.getElementById('typeform');
        if (form) {
            form.addEventListener('submit', this.formSubmission.handleSubmit.bind(this.formSubmission));
        }
    }

    loadStoredData() {
        const data = DataPersistence.load();
        if (Object.keys(data).length > 0) {
            DataPersistence.populateForm(data);
        }
    }

    // Public methods for button onclick handlers
    next() {
        this.stepManager.nextStep();
    }

    prev() {
        this.stepManager.prevStep();
    }

    restart() {
        DataPersistence.clearData();
        window.location.reload();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.typeformController = new TypeformController();
});

// Global functions for onclick handlers
function nextStep() {
    if (window.typeformController) {
        window.typeformController.next();
    }
}

function prevStep() {
    if (window.typeformController) {
        window.typeformController.prev();
    }
}
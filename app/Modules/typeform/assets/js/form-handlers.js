/**
 * Form handlers and utilities
 */
class FormHandlers {
    static bindEvents() {
        // Electronic signature conditional display
        document.addEventListener('change', (e) => {
            if (e.target.name === 'has_signature') {
                this.toggleSignatureUpload(e.target.value);
            }
        });

        // File upload handling
        document.querySelectorAll('.file-upload input[type="file"]').forEach(input => {
            input.addEventListener('change', this.handleFileUpload.bind(this));
        });

        // Input validation on blur
        document.querySelectorAll('input[required], select[required]').forEach(input => {
            input.addEventListener('blur', (e) => FormValidation.validateField(e.target));
        });

        // RUT formatting
        document.querySelectorAll('input[name="rut"], input[name="legal_rep_rut"]').forEach(input => {
            input.addEventListener('input', FormValidation.formatRUT);
        });

        // Phone number formatting
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', FormValidation.formatPhone);
        });
    }

    static toggleSignatureUpload(value) {
        const signatureUpload = document.getElementById('signature-upload');
        if (signatureUpload) {
            if (value === 'yes') {
                signatureUpload.style.display = 'block';
                signatureUpload.querySelector('input').required = true;
            } else {
                signatureUpload.style.display = 'none';
                signatureUpload.querySelector('input').required = false;
            }
        }
    }

    static handleFileUpload(e) {
        const uploadContent = e.target.nextElementSibling;
        if (e.target.files.length > 0) {
            const file = e.target.files[0];
            const maxSize = e.target.name === 'signature_file' ? 2 * 1024 * 1024 : 5 * 1024 * 1024;
            
            if (file.size > maxSize) {
                this.showError(`El archivo es demasiado grande. Máximo ${maxSize / 1024 / 1024}MB`);
                e.target.value = '';
                return;
            }

            uploadContent.innerHTML = `
                <div class="file-icon">✅</div>
                <p>Archivo seleccionado: ${file.name}</p>
                <span class="file-size">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            `;
        }
    }

    static showError(message) {
        alert(message); // In a real implementation, you'd want a proper notification system
    }
}
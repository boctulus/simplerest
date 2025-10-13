/**
 * Form validation utilities
 */
class FormValidation {
    static validateField(field) {
        const value = field.value.trim();
        let isValid = true;

        // Remove previous error state
        field.classList.remove('error');

        // Required field check
        if (field.hasAttribute('required') && !value) {
            isValid = false;
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
            }
        }

        // RUT validation
        if ((field.name === 'rut' || field.name === 'legal_rep_rut') && value) {
            if (!this.validateRUT(value)) {
                isValid = false;
            }
        }

        // Phone validation
        if (field.type === 'tel' && value) {
            const phoneRegex = /^[0-9]{8,9}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                isValid = false;
            }
        }

        if (!isValid) {
            field.classList.add('error');
        }

        return isValid;
    }

    static validateRUT(rut) {
        // Basic Chilean RUT validation
        rut = rut.replace(/[^0-9kK]/g, '');
        if (rut.length < 8) return false;
        
        const body = rut.slice(0, -1);
        const dv = rut.slice(-1).toUpperCase();
        
        let sum = 0;
        let multiplier = 2;
        
        for (let i = body.length - 1; i >= 0; i--) {
            sum += parseInt(body[i]) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
        }
        
        const remainder = sum % 11;
        const calculatedDV = remainder === 0 ? '0' : remainder === 1 ? 'K' : (11 - remainder).toString();
        
        return dv === calculatedDV;
    }

    static formatRUT(e) {
        let value = e.target.value.replace(/[^0-9kK]/g, '');
        if (value.length > 1) {
            value = value.slice(0, -1) + '-' + value.slice(-1);
        }
        if (value.length > 4) {
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        }
        e.target.value = value.toUpperCase();
    }

    static formatPhone(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 4) {
            value = value.replace(/(\d{4})(\d{4})/, '$1 $2');
        }
        e.target.value = value;
    }
}
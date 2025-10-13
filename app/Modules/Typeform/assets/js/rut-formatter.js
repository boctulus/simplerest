/**
 * RUT Formatter for Chilean RUT format
 * Formats RUT automatically as user types in XXXXXXXX-X format (without dots by default)
 * or XX.XXX.XXX-X format (with dots if specified)
 */
class RutFormatter {
    
    /**
     * Format a RUT string to XXXXXXXX-X format (without dots by default) or XX.XXX.XXX-X format (with dots)
     * @param {string} rut - The RUT string to format
     * @param {boolean} withDots - Whether to include dots (default: false)
     * @returns {string} - Formatted RUT
     */
    static formatRut(rut, withDots = false) {
        if (!rut) return '';
        
        // Remove all non-alphanumeric characters except K
        let cleanRut = rut.replace(/[^0-9Kk]/g, '');
        
        // Convert k to uppercase
        cleanRut = cleanRut.replace(/k/g, 'K');
        
        // Ensure we have at least 2 characters (number + check digit)
        if (cleanRut.length < 2) return cleanRut;
        
        // Split the main number from the check digit
        const mainNumber = cleanRut.slice(0, -1);
        const checkDigit = cleanRut.slice(-1);
        
        let formattedMainNumber;
        
        if (withDots) {
            // Add dots to the main number (every 3 digits from right)
            const reversedMainNumber = mainNumber.split('').reverse().join('');
            const chunked = reversedMainNumber.match(/.{1,3}/g) || [];
            formattedMainNumber = chunked.join('.').split('').reverse().join('');
        } else {
            // No dots, just the main number
            formattedMainNumber = mainNumber;
        }
        
        // Return formatted RUT with dash before check digit
        return `${formattedMainNumber}-${checkDigit}`;
    }
    
    /**
     * Clean RUT removing format characters
     * @param {string} rut - The formatted RUT
     * @returns {string} - Clean RUT without dots and dash
     */
    static cleanRut(rut) {
        if (!rut) return '';
        return rut.replace(/[.-]/g, '');
    }
    
    /**
     * Validate Chilean RUT
     * @param {string} rut - The RUT to validate
     * @returns {boolean} - True if valid, false otherwise
     */
    static validateRut(rut) {
        if (!rut) return false;
        
        const cleanRut = this.cleanRut(rut);
        if (cleanRut.length < 2) return false;
        
        const mainNumber = cleanRut.slice(0, -1);
        const checkDigit = cleanRut.slice(-1).toUpperCase();
        
        // Validate that main number contains only digits
        if (!/^\d+$/.test(mainNumber)) return false;
        
        // Calculate check digit
        const calculatedCheckDigit = this.calculateCheckDigit(mainNumber);
        
        return calculatedCheckDigit === checkDigit;
    }
    
    /**
     * Calculate check digit for a RUT
     * @param {string} mainNumber - The main number of the RUT
     * @returns {string} - The calculated check digit
     */
    static calculateCheckDigit(mainNumber) {
        let sum = 0;
        let multiplier = 2;
        
        // Calculate sum starting from right
        for (let i = mainNumber.length - 1; i >= 0; i--) {
            sum += parseInt(mainNumber.charAt(i)) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
        }
        
        const remainder = sum % 11;
        const checkDigit = 11 - remainder;
        
        if (checkDigit === 11) return '0';
        if (checkDigit === 10) return 'K';
        return checkDigit.toString();
    }
    
    /**
     * Initialize RUT formatting for all RUT input fields
     */
    static initRutFormatting() {
        // Find all RUT input fields
        const rutFields = document.querySelectorAll('input[name="rut"], input[name="legal_rep_rut"], input[id*="rut"]');
        
        rutFields.forEach(field => {
            this.attachRutFormatter(field);
        });
        
        console.log(`✅ RUT formatting initialized for ${rutFields.length} fields`);
    }
    
    /**
     * Attach RUT formatter to a specific field
     * @param {HTMLInputElement} field - The input field to attach formatting to
     */
    static attachRutFormatter(field) {
        // Set initial placeholder if not set
        if (!field.placeholder || field.placeholder === '12.345.678-9' || field.placeholder === '18.280.886-5') {
            field.placeholder = '18280886-5';
        }
        
        // Format on input
        field.addEventListener('input', (e) => {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = this.formatRut(oldValue);
            
            // Only update if the value actually changed
            if (newValue !== oldValue) {
                e.target.value = newValue;
                
                // Adjust cursor position
                const lengthDifference = newValue.length - oldValue.length;
                const newCursorPosition = cursorPosition + lengthDifference;
                e.target.setSelectionRange(newCursorPosition, newCursorPosition);
            }
        });
        
        // Validate on blur
        field.addEventListener('blur', (e) => {
            const rut = e.target.value.trim();
            if (rut && !this.validateRut(rut)) {
                // Add invalid class for styling
                e.target.classList.add('rut-invalid');
                
                // Show validation message
                this.showValidationMessage(e.target, 'RUT inválido');
            } else {
                e.target.classList.remove('rut-invalid');
                this.hideValidationMessage(e.target);
            }
        });
        
        // Format existing value if any
        if (field.value) {
            field.value = this.formatRut(field.value);
        }
    }
    
    /**
     * Show validation message for a field
     * @param {HTMLInputElement} field - The input field
     * @param {string} message - The validation message
     */
    static showValidationMessage(field, message) {
        // Remove existing message
        this.hideValidationMessage(field);
        
        const messageEl = document.createElement('div');
        messageEl.className = 'rut-validation-message';
        messageEl.textContent = message;
        messageEl.style.cssText = `
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        `;
        
        field.parentNode.appendChild(messageEl);
    }
    
    /**
     * Hide validation message for a field
     * @param {HTMLInputElement} field - The input field
     */
    static hideValidationMessage(field) {
        const existingMessage = field.parentNode.querySelector('.rut-validation-message');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    RutFormatter.initRutFormatting();
});

// Re-initialize when new content is loaded (for dynamic forms)
if (typeof window.stepManager !== 'undefined') {
    // Hook into step changes if stepManager exists
    const originalShowStep = window.stepManager?.showStep;
    if (originalShowStep) {
        window.stepManager.showStep = function(step) {
            const result = originalShowStep.call(this, step);
            // Re-initialize RUT formatting after step change
            setTimeout(() => RutFormatter.initRutFormatting(), 100);
            return result;
        };
    }
}
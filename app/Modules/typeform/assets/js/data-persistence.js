/**
 * Data persistence using localStorage
 */
class DataPersistence {
    static save(data) {
        localStorage.setItem('typeform_data', JSON.stringify(data));
    }

    static load() {
        const stored = localStorage.getItem('typeform_data');
        return stored ? JSON.parse(stored) : {};
    }

    static clearData() {
        localStorage.removeItem('typeform_data');
    }

    static populateForm(data) {
        Object.keys(data).forEach(name => {
            const elements = document.querySelectorAll(`[name="${name}"]`);
            elements.forEach(element => {
                if (element.type === 'checkbox' || element.type === 'radio') {
                    if (Array.isArray(data[name])) {
                        element.checked = data[name].includes(element.value);
                    } else {
                        element.checked = element.value === data[name];
                    }
                } else if (element.type !== 'file') {
                    element.value = data[name] || '';
                }
            });
        });
    }
}
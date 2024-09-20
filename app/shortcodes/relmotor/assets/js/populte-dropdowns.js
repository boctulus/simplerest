
/*
    Ej:
        const selector = $('#producto');
        populateSelect2(selector, options, { 'id': 'NULL', 'text': 'Categoría' }, ['Sin categorizar']);    
    }

*/
function populateSelect2(selector, options, default_option, exclude_options = []) {
    selector.empty(); // Limpiar el select por si hay datos previos

    // Si se pasa un default_option, agrégalo al principio
    if (default_option) {
        selector.append(new Option(default_option.text, default_option.id));
    }

    // Agregar cada categoría al select, excepto las que están en exclude_options
    $.each(options, function(key, value) {
        if (!exclude_options.includes(value)) {
            selector.append(new Option(value, key));
        }
    });

    // Inicializar Select2 (si aún no está inicializado)
    if (!selector.hasClass('select2-hidden-accessible')) {
        selector.select2({
            placeholder: default_option ? default_option.text : 'Seleccionar',
            allowClear: true
        });
    }
}

function fetchCategories() {
    const cachedCategories = sessionStorageCache.getItem('se-categories');

    if (cachedCategories) {
        console.log('Usando datos en caché');
        populateCategories(cachedCategories);
    } else {
        // No hardocodear URL
        fetch('http://relmotor.lan/woo_commerce_filters/categories', {
            method: 'GET', // o 'POST' si es necesario
            headers: {
                'Content-Type': 'application/json',
                'User-Agent': 'PostmanRuntime/7.34.0' 
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const categories = data.data;
            sessionStorageCache.setItem('se-categories', categories, 3600); // Guardar en caché por 1 hora
            populateCategories(categories);
        })
        .catch(error => console.error('Error al obtener categorías:', error));
    }
}

function fetchAttributes() {
    const atts = sessionStorageCache.getItem('se-attributtes');

    if (atts) {
        console.log('Usando datos en caché');
        populateAttributes(atts);
    } else {
        // No hardocodear URL base !!
        
        const attributes = att_names.map((att) => {return att.replace(' ', '%20')}).join(',');
        
        fetch(`http://relmotor.lan/woo_commerce_filters/attributes/${attributes}`, {
            method: 'GET', 
            headers: {
                'Content-Type': 'application/json',
                'User-Agent': 'PostmanRuntime/7.34.0' 
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const atts = data.data;
            sessionStorageCache.setItem('se-attributtes', atts, 3600); // Guardar en caché por 1 hora
            populateAttributes(atts);
        })
        .catch(error => console.error('Error al obtener atributos:', error));
    }
}

function populateCategories(options) {
    const selector = $('#producto');
    populateSelect2(selector, options, { 'id': 'NULL', 'text': 'Categoría'.toUpperCase() }, ['Sin categorizar']);    
}

function populateAttributes(options) {
    ['Marca', 'Sistema Eléctrico'].forEach(name => {
        const selector = $(`[data-id='${name}']`);

        if (selector.length > 0) {
            populateSelect2(selector, options[name], { 'id': 'NULL', 'text': name.toUpperCase() });
        } else {
            console.warn(`No se encontró el selector con data-id='${name}'`);
        }
    });
}


// Llamar a la función al cargar la página
jQuery(document).ready(function() {
    $('#producto').select2({
        theme: 'bootstrap-5'
    });

    // Los atributos los selecciono por el valor de su data-id
    // (no uso id)

    att_names.forEach((name) => {
        $(`[data-id="${name}"]`).select2({
            theme: 'bootstrap-5'
        });
    });

    fetchCategories();
    fetchAttributes();
});

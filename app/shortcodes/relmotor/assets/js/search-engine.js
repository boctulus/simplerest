// Función principal de búsqueda
function searchProducts() {
    const keywords = $('#anything').val().trim();
    const categoryId = $('#producto').val();
    const systemElectrico = $('[data-id="Sistema Eléctrico"]').val();
    const marca = $('[data-id="Marca"]').val();
    const codigo = $('#buscar-codigo').val().trim();
    const enOferta = $('#oferta').is(':checked');
    const enStock = $('#stock').is(':checked');

    const params = {
        user_id: user_id
    };

    if (keywords) params.keywords = keywords;
    if (enStock) params.in_stock = true;

    // Modificamos la lógica para el filtro de oferta
    if (enOferta) {
        params.attributes = params.attributes || {};
        params.attributes['Precio Especial'] = 'Oferta';
    }

    // Solo agregar atributos si tienen un valor válido
    if (systemElectrico && systemElectrico !== 'NULL') {
        params.attributes = params.attributes || {};
        params.attributes['Sistema Eléctrico'] = systemElectrico;
    }
    if (marca && marca !== 'NULL') {
        params.attributes = params.attributes || {};
        params.attributes['Marca'] = marca;
    }

    if (categoryId && categoryId !== 'NULL') params.category_id = categoryId;
    if (codigo) params.wp_attributes = { '_sku': codigo };

    console.log('Parámetros de búsqueda:', params);

    // Realizar la búsqueda
    $.ajax({
        url: 'http://relmotor.lan/woo_commerce_filters/product_search',
        method: 'GET',
        data: params,
        success: function(response) {
            console.log('Respuesta de la API:', response);
            displayResults(response.data, enStock, enOferta);
        },
        error: function(xhr, status, error) {
            console.error("Error en la búsqueda:", error);
            console.log('Detalles del error:', xhr.responseText);
        }
    });
}

// Función para mostrar los resultados
function displayResults(products, enStock, enOferta) {
    const resultsContainer = $('.results-container tbody');
    resultsContainer.empty();

    let displayedCount = 0;

    products.forEach(product => {
        // Verificar si el producto tiene stock cuando el filtro está activado
        if (enStock && (!product.stock_quantity || product.stock_quantity <= 0)) {
            return; // Saltar este producto si no tiene stock
        }

        // Verificar si el producto está en oferta cuando el filtro está activado
        if (enOferta) {
            const precioEspecial = product.attributes.find(attr => attr.name === "Precio Especial");
            if (!precioEspecial || precioEspecial.value !== "Oferta") {
                return; // Saltar este producto si no está en oferta
            }
        }

        // Obtener las categorías y convertirlas a string
        const categoriesPromises = product.category_ids 
            ? product.category_ids.map(id => getCategoryName(id))
            : Promise.resolve([]);
        
        Promise.all(categoriesPromises).then(categories => {
            const categoriesString = categories.join(', ');
            
            // Obtener la marca del producto
            const marca = product.attributes.find(attr => attr.name === "Marca");
            const marcaValue = marca ? marca.value : '';

            const row = `
                <tr>
                    <td><img src="${product.featured_image || ''}" alt="${product.name}" class="img-fluid results-image"></td>
                    <td class="results-code">${product.sku || ''}</td>
                    <td class="d-none d-md-table-cell">${categoriesString}</td>
                    <td class="d-none d-lg-table-cell results-description">
                        ${product.name || ''}
                        <br>
                        <strong>Referencia Cruzada ·</strong>
                    </td>
                    <td class="d-none d-xl-table-cell">${marcaValue}</td>
                    <td>
                        <div class="results-price-list">Precio lista: $${product.regular_price || ''}</div>
                        <div class="results-price-discount">PRECIO CON DTO:</div>
                        <div class="results-price">$${product.price || ''} <small>Neto</small></div>
                    </td>
                    <td>
                        <div class="input-group mb-2">
                            <button class="btn btn-outline-secondary results-quantity-btn" type="button">-</button>
                            <input type="number" class="form-control results-quantity" value="0" min="0" max="99999">
                            <button class="btn btn-outline-secondary results-quantity-btn" type="button">+</button>
                        </div>
                        <button class="btn btn-success w-100 mb-2"><i class="fas fa-cart-plus"></i> Añadir</button>
                        <div class="stock-info">STOCK: ${product.stock_quantity || 'N/A'}</div>
                    </td>
                </tr>
            `;
            resultsContainer.append(row);
            displayedCount++;
        });
    });

    updateResultCount(displayedCount);
}


function getCategoryName(categoryId) {
    let categories = sessionStorageCache.getItem('se-categories');
    
    if (!categories) {
        // Si no hay categorías en caché, las obtenemos
        fetchCategories().then(() => {
            categories = sessionStorageCache.getItem('se-categories');
            return categories[categoryId] || categoryId.toString();
        });
    } else {
        return categories[categoryId] || categoryId.toString();
    }
}


// Función para actualizar el contador de resultados
function updateResultCount(count) {
    let message;
    if (count === 0) {
        message = "No se encontraron resultados";
    } else if (count === 1) {
        message = "Mostrando el único resultado";
    } else {
        message = `Mostrando ${count} resultados`;
    }
    $('.result-count').text(message);
}

// Event listeners
$(document).ready(function() {
    // Evento de búsqueda solo en submit del formulario
    $('form').on('submit', function(e) {
        e.preventDefault();
        console.log('Formulario enviado - Iniciando búsqueda');
        searchProducts();
    });

    // Evento de limpieza
    $('button:contains("Limpiar")').on('click', function() {
        $('form')[0].reset();
        $('.results-container tbody').empty();
        updateResultCount(0);
        
        // Deseleccionar todos los SELECT2
        $('select').val(null).trigger('change');
        
        console.log('Formulario limpiado');
    });

    // Inicializar Select2 para los dropdowns
    $('select').select2({
        theme: 'bootstrap-5'
    });

    // Ya no necesitamos estos event listeners para búsqueda automática
    // $('select').on('change', function() {
    //     searchProducts();
    // });
    // $('input[type="checkbox"]').on('change', function() {
    //     searchProducts();
    // });
    // $('#anything').on('input', debounce(function() {
    //     searchProducts();
    // }, 300));
    // $('#buscar-codigo').on('input', debounce(function() {
    //     searchProducts();
    // }, 300));
});


// Función de debounce para evitar muchas llamadas seguidas
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
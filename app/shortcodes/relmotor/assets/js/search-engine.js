// Variables globales para la paginación
let currentPage = 1;
let pageSize = 10; // 
let totalResults = 0;
let allProducts = []; // Almacenará todos los productos recibidos de la API
let currentPaginator = null;

// Función principal de búsqueda
function searchProducts(page = 1) {
    const keywords = $('#anything').val().trim();
    const categoryId = $('#producto').val();
    const systemElectrico = $('[data-id="Sistema Eléctrico"]').val();
    const marca = $('[data-id="Marca"]').val();
    const codigo = $('#buscar-codigo').val().trim();
    const enOferta = $('#oferta').is(':checked');
    const enStock = $('#stock').is(':checked');

    const params = {
        user_id: user_id,
        page: page,
        per_page: pageSize
    };

    if (keywords) params.keywords = keywords;
    if (enStock) params.in_stock = true;

    if (enOferta) {
        params.attributes = params.attributes || {};
        params.attributes['Precio Especial'] = 'Oferta';
    }

    if (systemElectrico && systemElectrico !== 'NULL') {
        params.attributes = params.attributes || {};
        params.attributes['Sistema Eléctrico'] = systemElectrico;
    }
    if (marca && marca !== 'NULL') {
        params.attributes = params.attributes || {};
        params.attributes['Marca'] = getAttributesByName('Marca')[marca];
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
            currentPaginator = response.paginator;
            displayResults(response.data);
            updatePagination(currentPaginator);
        },
        error: function(xhr, status, error) {
            console.error("Error en la búsqueda:", error);
            console.log('Detalles del error:', xhr.responseText);
        }
    });
}

// Función para mostrar los resultados de una página específica
function displayResultsPage(page) {
    if (currentPaginator) {
        searchProducts(page);
    } else {
        console.error("No hay información de paginación disponible");
    }
    updatePagination();
}

// Función para mostrar los resultados
function displayResults(products) {
    const resultsContainer = $('.results-container tbody');
    resultsContainer.empty();

    let displayedCount = 0;

    products.forEach(product => {
        // Verificar si el producto tiene stock cuando el filtro está activado
        if ($('#stock').is(':checked') && (!product.stock_quantity || product.stock_quantity <= 0)) {
            return; // Saltar este producto si no tiene stock
        }

        // Verificar si el producto está en oferta cuando el filtro está activado
        if ($('#oferta').is(':checked')) {
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

    updateResultCount(products.length, currentPaginator);
}

// Función para actualizar la paginación
function updatePagination(paginator) {
    if (paginator.total > 0) {
        const data = {
            paginator: {
                current_page: paginator.current_page ?? 1,
                last_page: Math.ceil(paginator.total / pageSize),
                total: paginator.total
            }
        };
        BootstrapPaginator.render(data, '#pagination-container', 5, true);
        $('#pagination-container').show();
    } else {
        $('#pagination-container').hide();
    }
}

// Función para actualizar el contador de resultados
function updateResultCount(count, paginator = null) {
    let message;
    if (count === 0) {
        message = "No se encontraron resultados";
    } else if (count === 1) {
        message = "Mostrando el único resultado";
    } else if (paginator) {
        const start = (paginator.current_page - 1) * pageSize + 1;
        const end = Math.min(paginator.current_page * pageSize, paginator.total);
        message = `Mostrando ${start}-${end} de ${paginator.total} resultados`;
    } else {
        message = `Mostrando ${count} resultados`;
    }
    $('.result-count').text(message);
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

// Ej: getAttributesByName('Marca')
function getAttributesByName(att_name) {
    let atts = sessionStorageCache.getItem('se-attributtes');
    
    if (!atts) {
        // Si no hay categorías en caché, las obtenemos
        fetchAttributes().then(() => {
            atts = sessionStorageCache.getItem('se-attributtes');
            return atts[att_name] || false;
        });
    } else {
        return atts[att_name] ?? false;
    }
}

// Event listeners
$(document).ready(function() {
    // Evento de búsqueda
    $('form').on('submit', function(e) {
        e.preventDefault();
        console.log('Formulario enviado - Iniciando búsqueda');
        searchProducts();
    });

    // Evento de limpieza
    $('button:contains("Limpiar")').on('click', function() {
        $('form')[0].reset();
        $('select').val(null).trigger('change');
        $('.results-container tbody').empty();
        $('#pagination-container').hide();
        updateResultCount(0);
        currentPaginator = null;
        console.log('Formulario limpiado');
    });

    // Evento de cambio de página
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            searchProducts(page);
        }
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
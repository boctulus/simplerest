<h3 class="mt-3 text-primary">MY DATATABLE</h3>


<div style="min-width: 150px; padding: 1rem; position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(0px, 40px, 0px); background-color: rgba(1,1,1,0.5);" data-popper-placement="bottom-end">                
    <!-- Los checkboxes se generarán dinámicamente desde tableConfig -->
                
    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-image" data-column="image" checked="">
        <label class="form-check-label" for="col-image" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            IMÁGEN
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-code" data-column="code" checked="">
        <label class="form-check-label" for="col-code" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            CÓDIGO
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-product" data-column="product" checked="">
        <label class="form-check-label" for="col-product" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            PRODUCTO
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-description" data-column="description" checked="">
        <label class="form-check-label" for="col-description" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            DESCRIPCIÓN
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-brand" data-column="brand" checked="">
        <label class="form-check-label" for="col-brand" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            MARCA
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-price" data-column="price" checked="">
        <label class="form-check-label" for="col-price" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            PRECIO
        </label>
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input column-toggle" type="checkbox" id="col-addToCart" data-column="addToCart" checked="">
        <label class="form-check-label" for="col-addToCart" style="color: rgb(51, 51, 51); font-weight: 500; text-transform: uppercase; font-size: 0.8rem;">
            COMPRAR
        </label>
    </div>
</div>

<!-- 
    Datatabla dinamica 
-->
<table id="exampleTable" class="table table-striped table-hover mt-3"></table>


<script>
document.addEventListener("DOMContentLoaded", (event) => {
     // Configuración de la tabla con cabeceras y clases Bootstrap 5
    const datatable = new CustomDataTable('exampleTable', 'id', [
        { 
            htmlContent: '<div class="th-content">IMÁGEN</div>', 
            key: 'image', 
            cssClasses: ['image-cell']
        },
        { 
            htmlContent: '<div class="th-content">CÓDIGO</div>', 
            key: 'code', 
            cssClasses: ['results-code'],
            hideBreakpoints: ['sm', 'md'] // Visible desde lg
        },
        { 
            htmlContent: '<div class="th-content">PRODUCTO</div>', 
            key: 'product', 
            cssClasses: ['product-cate'],
            hideBreakpoints: ['sm', 'md', 'lg'] // Visible desde xl
        },
        { 
            htmlContent: '<div class="th-content">DESCRIPCIÓN</div>', 
            key: 'description', 
            cssClasses: ['results-description'],
            hideBreakpoints: ['sm', 'md'] // Visible desde lg
        },
        { 
            htmlContent: '<div class="th-content">MARCA</div>', 
            key: 'brand', 
            cssClasses: ['brand-cell'],
            hideBreakpoints: ['sm', 'md', 'lg'] // Visible desde xl
        },
        { 
            htmlContent: '<div class="th-content">PRECIO</div>', 
            key: 'price', 
            cssClasses: ['price_list']
        },
        { 
            htmlContent: 'COMPRAR', 
            key: 'addToCart', 
            cssClasses: ['add2cart']
        }
    ]);
    
    // Agregar una fila de datos con contenido HTML dinámico
    datatable.set({
        id: 88045,
        image: '<img src="http://relmotor.lan/wp-content/uploads/2023/01/9726f13424-350x350.jpg" alt="PORTA CARBON UNIFAP" class="img-fluid results-image" data-bs-toggle="modal" data-bs-target="#productQuickView">',
        code: '13424',
        product: 'PORTA CARBONES MP.',
        description: '<div class="mb-2"><strong>Descripción:</strong> 5x18x15 mm - SILVERADO 10453919, 69121</div>',
        brand: '<img src="http://relmotor.lan/wp-content/uploads/2022/04/logo-unifap-100x100.png" alt="UNIFAP" class="brand-logo">',
        price: '<div class="mb-2 results-sku">SKU: 13424</div><div class="results-price-list">Precio lista: <span class="results-price-value">$10.498</span></div>',
        addToCart: `
            <div class="quantity-control d-flex align-items-center justify-content-between mb-2">
                <button class="btn btn-outline-secondary input-quantity-btn" type="button" data-action="decrease">-</button>
                <input type="number" class="form-control input-quantity" value="0" min="0" max="5" data-product-id="88045">
                <button class="btn btn-outline-secondary input-quantity-btn" type="button" data-action="increase">+</button>
            </div>
            <button class="btn btn-success w-100 mb-2 add-to-cart-btn" data-product-id="88045">
                <i class="fas fa-cart-plus"></i> Añadir
                <span class="badge badge-light incart_counter invisible" data-product-id="88045">&nbsp;&nbsp;&nbsp;</span>  
            </button>
            <div class="stock-info" data-product-id="88045" data-original-stock="5" data-current-stock="5"><span class="display_instock">STOCK: 5</span></div>
        `
        },

        {
            id: 128048, 
            image: `<img src="http://relmotor.lan/wp-content/uploads/2024/08/992591986AE0100-247x296.jpg" 
                alt="REGULADOR BOSCH - VOLVO - 80A/110A (F00M144118, F00MA45248)" class="img-fluid results-image" 
                data-bs-toggle="modal" data-bs-target="#productQuickView" 
                data-fullsize="http://relmotor.lan/wp-content/uploads/2024/08/992591986AE0100-247x296.jpg" 
                decoding="async" 
                onerror="this.onerror=null; this.src='/wp-content/uploads/2021/09/imagen-no-disponible.png';">`,
            code: `<a href="http://relmotor.lan/producto/regulador-seg-volvo-80a-110a-1986ae0100/">1.986.AE0.100</a>`, 
            category: `REGULADOR`, 
            description: `<div class="mb-2 collapsible-description">
                            <strong>Descripción:</strong>
                            <div class="truncated-content">(F00M144118, F00MA45248) - 24V - VOLVO, DAF, RENAULT...</div>
                            <div class="collapse full-content" id="description-0">
                                <div>(F00M144118, F00MA45248) - 24V - VOLVO, DAF, RENAULT...</div>
                            </div>
                        </div>
                        <div class="mb-2"><strong>Referencia Plaza:</strong> 1986AE0100</div>`,
            brand: `<img src="http://relmotor.lan/wp-content/uploads/2022/03/logo-bosch-100x100.png" alt="BOSCH" class="brand-logo">`,
            price: `<div class="mb-2 results-sku">SKU: 1.986.AE0.100</div>
                <div class="results-price-list">Precio lista: <span class="results-price-value" style="text-decoration: line-through;">$35.900</span></div>
                <div class="results-price-discount">PRECIO:</div>
                <div class="results-price">$21.540 <small>Neto</small></div>`,
            addToCart: `<div class="quantity-control d-flex align-items-center justify-content-between mb-2">
                <button class="btn btn-outline-secondary input-quantity-btn" type="button" data-action="decrease">-</button>
                    <input type="number" class="form-control input-quantity" value="0" min="0" max="96" data-product-id="128048">
                <button class="btn btn-outline-secondary input-quantity-btn" type="button" data-action="increase">+</button>
                </div>
                <button class="btn btn-success w-100 mb-2 add-to-cart-btn" data-product-id="128048">
                    <i class="fas fa-cart-plus"></i> Añadir
                    <span class="badge badge-light incart_counter invisible" data-product-id="128048">&nbsp;&nbsp;&nbsp;</span>
                </button>
                <div class="stock-info" data-product-id="128048" data-original-stock="96" data-current-stock="96">
                    <span class="display_instock">STOCK: 96</span>
                </div>`
        }
        
            // otras rows    
        );


        // Pruebas de actualización parcial

        setTimeout(() => {
            console.log("Actualizando fila con ID 88045..."); // Cambiar ID 1 por 88045
            datatable.updateRow({
                id: 88045, // Cambiar ID 1 por 88045
                price: '<div class="results-price-list">Precio lista: <span class="results-price-value">$15.000</span></div>',
                product: 'PORTA CARBONES MP. ACTUALIZADO'
            });

            console.log("Actualizando fila con ID 128048..."); // Cambiar ID 2 por 128048
            datatable.updateRow({
                id: 128048, // Cambiar ID 2 por 128048
                description: '<div class="mb-2"><strong>Descripción:</strong> Nueva descripción actualizada</div>',
                brand: '<img src="http://relmotor.lan/wp-content/uploads/2022/03/logo-bosch-100x100.png" alt="BOSCH UPDATED" class="brand-logo">'
            });
        }, 1500);


        // Manejar eventos de los checkboxes
        $(document).on('change', '.column-toggle', function() {
            const columnKey = $(this).data('column');
            const isVisible = $(this).prop('checked');
            
            datatable.toggleColumnVisibility(columnKey, isVisible);
        });

    });
</script>
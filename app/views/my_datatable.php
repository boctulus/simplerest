<h3 class="mt-3 text-primary">MY DATATABLE</h3>

<!-- 
    Datatabla dinamica 
-->
<table id="exampleTable" class="table table-striped table-hover mt-3"></table>


<script>
    /*
        La clase CustomDataTable permite crear una tabla HTML dinámica, controlando la configuración de cabeceras (<th>), las filas de datos, y el "partial rendering" de filas sin volver a renderizar toda la tabla. 
        
        Esta implementación es compatible con Bootstrap 5, permitiendo personalizar clases CSS para cabeceras y columnas, y actualizando columnas específicas de forma eficiente.

        La mayoría de las implementaciones estándar de DataTables, como jQuery DataTables o DataTables.net, no soportan directamente la actualización parcial de filas sin volver a renderizar la fila completa.
        
        Las opciones comunes para manipular datos en estas bibliotecas incluyen métodos como row().data() para actualizar la fila, pero este enfoque típicamente requiere el redibujado completo de la fila, lo que puede afectar el rendimiento en tablas grandes o cuando se necesita una actualización precisa sin perder el estado de celdas específicas (como clases CSS aplicadas dinámicamente).

        https://chatgpt.com/c/67227701-d6b8-800d-9fcd-7485330cdf19   
    */

    class CustomDataTable {
    constructor(tableId, headers) {
        this.table = document.getElementById(tableId);
        this.headers = headers;
        this.rows = new Map();

        // Inicializar la tabla con las cabeceras
        this.initTable();
    }

    // Inicializar tabla con cabecera HTML completa
    initTable() {
        const thead = this.table.createTHead();
        const headerRow = thead.insertRow();

        this.headers.forEach(header => {
            const th = document.createElement('th');
            th.classList.add(...(header.cssClasses || []));
            th.innerHTML = header.htmlContent; // Permitir contenido HTML completo para cada th
            headerRow.appendChild(th);
        });

        this.table.createTBody(); // Crear tbody vacío para las filas
    }

    // Agregar filas dinámicamente con contenido HTML en celdas
    set(...rows) {
        rows.forEach(rowData => {
            const { id } = rowData;
            if (!id) throw new Error("Cada fila debe tener un 'id' único.");

            // Verificar si la fila existe y actualizar si es necesario
            if (this.rows.has(id)) {
                this.updateRow(rowData);
            } else {
                this.addRow(rowData);
            }
        });
    }

    // Agregar una fila con datos HTML
    addRow(rowData) {
        const tbody = this.table.tBodies[0];
        const newRow = tbody.insertRow();
        newRow.dataset.productId = rowData.id;

        this.headers.forEach(header => {
            const td = newRow.insertCell();
            td.classList.add(...(header.cssClasses || []));
            td.innerHTML = rowData[header.key] || ''; // Rellenar con contenido HTML
        });

        this.rows.set(rowData.id, newRow); // Guardar la fila para futuras actualizaciones
    }

    // Actualizar fila existente con datos HTML
    updateRow(rowData) {
        const existingRow = this.rows.get(rowData.id);

        this.headers.forEach(header => {
            const cellIndex = this.headers.findIndex(h => h.key === header.key);
            if (cellIndex !== -1 && rowData[header.key] !== undefined) {
                const cell = existingRow.cells[cellIndex];
                cell.innerHTML = rowData[header.key];
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", (event) => {

    // Configuración de la tabla con cabeceras y clases Bootstrap 5
    const datatable = new CustomDataTable('exampleTable', [
        { 
            htmlContent: '<div class="th-content">IMÁGEN <img src="/path/to/imagen.svg" class="table-head-icon"></div>', 
            key: 'image', 
            cssClasses: ['image-cell'] },
        { 
            htmlContent: '<div class="th-content">CÓDIGO <img src="/path/to/codigo.svg" class="table-head-icon"></div>', 
            key: 'code', 
            cssClasses: ['d-none', 'd-lg-table-cell', 'results-code'] },
        { 
            htmlContent: '<div class="th-content">PRODUCTO <img src="/path/to/producto.svg" class="table-head-icon"></div>', 
            key: 'product', 
            cssClasses: ['d-none', 'd-xl-table-cell', 'product-cate'] },
        { 
            htmlContent: '<div class="th-content">DESCRIPCIÓN <img src="/path/to/descripcion.svg" class="table-head-icon"></div>', 
            key: 'description', 
            cssClasses: ['d-none', 'd-lg-table-cell', 'results-description'] },
        { 
            htmlContent: '<div class="th-content">MARCA <img src="/path/to/marca.svg" class="table-head-icon"></div>', 
            key: 'brand', 
            cssClasses: ['d-none', 'd-xl-table-cell', 'brand-cell'] },
        { 
            htmlContent: '<div class="th-content">PRECIO <img src="/path/to/precio.svg" class="table-head-icon"></div>', 
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
        });
    });



</script>
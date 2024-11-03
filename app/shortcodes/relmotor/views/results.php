<div class="results-container rounded border mt-3">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
        <p class="result-count mb-0">Mostrando el único resultado</p>
        <select class="form-select results-select ms-2">
            <option value="popularity-asc">Ordenar por Popularidad</option>
            <option value="date-desc">Ordenar por Fecha: últimos primero</option>
            <option value="price-asc">Ordenar por Precio: bajo a alto</option>
            <option value="price-desc">Ordenar por Precio: alto a bajo</option>
            <option value="title-asc">Ordenar por Título: A - Z</option>
            <option value="title-desc">Ordenar por Título: Z - A</option>
            <option value="sku-asc">Ordenar por Código: menor a mayor</option>
            <option value="sku-desc">Ordenar por Código: mayor a menor</option>
            <option value="stock_quantity-desc">Ordenar por Disponibilidad: mayor a menor</option>
            <option value="stock_quantity-asc">Ordenar por Disponibilidad: menor a mayor</option>
            <option value="no-order" selected>Sin ordenar (la opción màs ràpida)</option>
        </select>
    </div>

    <div class="table-responsive">
        <div id="pagination-container" style="display: none;"></div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>IMÁGEN</th>
                    <th>CÓDIGO</th>
                    <th class="d-none d-md-table-cell">PRODUCTO</th>
                    <th class="d-none d-lg-table-cell">DESCRIPCIÓN</th>
                    <th class="d-none d-xl-table-cell">MARCA</th>
                    <th>PRECIO</th>
                    <th>COMPRAR</th>
                </tr>
            </thead>
            <tbody>
                <!-- row de demostracion que se mantiene de momento -->
                <tr data-product-id="116791">
                        <td class="image-cell ">
                        
                    
                    <span class="onsale">¡Oferta!</span>
                    <img src="http://relmotor.lan/wp-content/uploads/2024/03/e30f525-1105-350x350.jpg" alt="INDUCIDO RELPARTS - 29MT 24V - MERCEDES BENZ" class="img-fluid results-image" data-bs-toggle="modal" data-bs-target="#productQuickView" data-fullsize="http://relmotor.lan/wp-content/uploads/2024/03/e30f525-1105.jpg" decoding="async" onerror="this.onerror=null; this.src='/wp-content/uploads/2021/09/imagen-no-disponible.png';">
                        </td>
                        <td class="d-none d-lg-table-cell results-code">25-1105-K</td>
                        <td class="d-none d-xl-table-cell product-cate">INDUCIDO</td>
                        <td class="d-none d-lg-table-cell results-description">
                            
    <div class="mb-2 collapsible-description">
        <strong>Descripción:</strong>
        <div class="truncated-content">29MT 24V - L162, C60, SPL12, D23 - MERCEDES BENZ, CUMMINS - 10515834, RAS5250N - (ELE141698, ELE0425...</div>
        <div class="collapse full-content" id="description-0">
            <div class="">29MT 24V - L162, C60, SPL12, D23 - MERCEDES BENZ, CUMMINS - 10515834, RAS5250N - (ELE141698, ELE042524) - USADO EN 8200004, 8200054, 8200064, 8200065, 8200071, 8200138, 8200141, 8200222, 8200231, 8200257, 8200278, 8200292</div>
        </div>
        <button class="btn btn-link btn-sm p-0 mt-1 toggle-content" type="button" data-bs-toggle="collapse" data-bs-target="#description-0" aria-expanded="false" aria-controls="description-0">
            Mostrar más
        </button>
    </div>
    
                            
                        </td>
                        <td class="d-none d-xl-table-cell brand-cell">
                            <img src="http://relmotor.lan/wp-content/uploads/2022/03/logo-relparts-100x100.png" alt="RELPARTS" class="brand-logo" decoding="async">
                        </td>
                        <td class="price_list">
                            <div class="mb-2 results-sku">SKU: 25-1105-K</div>
                            <div class="results-price-list">Precio lista: <span class="results-price-value">$31.900</span></div>
                            <div class="results-price-discount">¡Oferta Especial!</div>
                            <div class="results-price">$18.200 <small>Neto</small></div>
                        </td>
                        <td class="add2cart">
                            <div class="quantity-control d-flex align-items-center justify-content-between mb-2">
                                <button class="btn btn-outline-secondary input-quantity-btn" type="button" data-action="decrease">-</button>
                                <input type="number" class="form-control input-quantity" value="0" min="0" max="192" data-product-id="116791">
                                <button class="btn btn-outline-secondary input-quantity-btn" type="button" data-action="increase">+</button>
                            </div>
                            
                            <!-- Debo actualizar el badge con la cantidad en el carrito  agregada por updateStockDisplayFromCart() -->
                            <button class="btn btn-success w-100 mb-2 add-to-cart-btn" data-product-id="116791">
                            <i class="fas fa-cart-plus"></i> Añadir
                            
                            <!-- badge -->
                            <span class="badge badge-light incart_counter invisible" data-product-id="116791">&nbsp;&nbsp;&nbsp;</span>  
                            <span class="sr-only">cant. en carrito</span>                            
                            </button>

                            <div class="stock-info" data-product-id="116791" data-original-stock="192" data-current-stock="192"><span class="display_instock">STOCK: 192</span></div>
                        </td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>



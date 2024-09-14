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
            <option value="stock_quantity-desc">Ordenar por Disponibilidad: Mayor a Menor</option>
            <option value="stock_quantity-asc">Ordenar por Disponibilidad: Menor a Mayor</option>
            <option value="no-order" selected>Sin ordenar (la opción màs ràpida)</option>
        </select>
    </div>

    <div class="table-responsive">
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
                <tr>
                    <td><img src="<?= shortcode_asset(__DIR__ . '/img/demo/ae18610-1467.jpg') ?>" alt="Producto"
                            class="img-fluid results-image"></td>
                    <td class="results-code">191007</td>
                    <td class="d-none d-md-table-cell">VARIOS</td>
                    <td class="d-none d-lg-table-cell results-description">
                        FUNDA PLASTICA CUBRE TERMINAL LUCAS HEMBRA
                        <br>
                        <strong>Referencia Cruzada ·</strong>
                    </td>
                    <td class="d-none d-xl-table-cell"><img
                            src="<?= shortcode_asset(__DIR__ . '/img/demo/logo-relparts-350x350.png') ?>" alt="Cargo"
                            width="80"></td>
                    <td>
                        <div class="results-price-list">Precio lista: $100</div>
                        <div class="results-price-discount">PRECIO CON DTO:</div>
                        <div class="results-price">$100 <small>Neto</small></div>
                    </td>
                    <td>
                        <div class="input-group mb-2">
                            <button class="btn btn-outline-secondary results-quantity-btn" type="button">-</button>
                            <input type="number" class="form-control results-quantity" value="0" min="0" max="99999">
                            <button class="btn btn-outline-secondary results-quantity-btn" type="button">+</button>
                        </div>
                        <button class="btn btn-success w-100 mb-2"><i class="fas fa-cart-plus"></i> Añadir</button>
                        <div class="stock-info">STOCK: 43029</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
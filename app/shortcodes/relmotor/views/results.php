<div class="results-container rounded border mt-3">
    <p class="result_count mt-4 mb-4">Mostrando el único resultado</p>
    <select class="form-select mb-3">
        <option selected>Ordenar por Disponibilidad: Mayor a Menor</option>
        <!-- Otras opciones aquí -->
    </select>
    <table class="results-table table table-bordered rounded">
        <thead>
            <tr>
                <th>IMAGEN</th>
                <th>CÓDIGO</th>
                <th>PRODUCTO</th>
                <th>DESCRIPCIÓN</th>
                <th>MARCA</th>
                <th>PRECIO</th>
                <th>COMPRAR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><img src="<?= shortcode_asset(__DIR__ .'/img/demo/ae18610-1467.jpg') ?>" alt="Producto" class="results-image"></td>
                <td class="results-code">191007</td>
                <td>VARIOS</td>
                <td class="results-description">
                    FUNDA PLASTICA CUBRE TERMINAL LUCAS HEMBRA
                    <br>
                    <strong>Referencia Cruzada ·</strong>
                </td>
                <td><img src="<?= shortcode_asset(__DIR__ .'/img/demo/logo-relparts-350x350.png') ?>" alt="Cargo" width="80"></td>
                <td>
                    <div class="results-price-list">Precio lista: $100</div>
                    <div class="results-price-discount">PRECIO CON DTO:</div>
                    <div class="results-price">$100 <small>Neto</small></div>
                </td>
                <td>
                    <input type="number" value="0" min="0" class="results-quantity form-control">
                    <button class="results-button btn btn-success">Añadir</button>
                    <div class="results-stock">STOCK: 43029</div>
                </td>
            </tr>

            <tr>
                <td><img src="<?= shortcode_asset(__DIR__ .'/img/demo/ae18610-1467.jpg') ?>" alt="Producto" class="results-image"></td>
                <td class="results-code">191007</td>
                <td>VARIOS</td>
                <td class="results-description">
                    FUNDA PLASTICA CUBRE TERMINAL LUCAS HEMBRA
                    <br>
                    <strong>Referencia Cruzada ·</strong>
                </td>
                <td><img src="<?= shortcode_asset(__DIR__ .'/img/demo/logo-relparts-350x350.png') ?>" alt="Cargo" width="80"></td>
                <td>
                    <div class="results-price-list">Precio lista: $100</div>
                    <div class="results-price-discount">PRECIO CON DTO:</div>
                    <div class="results-price">$100 <small>Neto</small></div>
                </td>
                <td>
                    <input type="number" value="0" min="0" class="results-quantity form-control">
                    <button class="results-button btn btn-success">Añadir</button>
                    <div class="results-stock">STOCK: 43029</div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

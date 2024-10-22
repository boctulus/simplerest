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
                <tr data-product-id="26653">
                    <td class="image-cell">
                        <img src="http://relmotor.lan/wp-content/uploads/2024/07/91c91S9128-350x350.jpg"
                            alt="MOTOR PARTIDA AS - 24V T11 - APLICACION HINO" class="img-fluid results-image"
                            data-bs-toggle="modal" data-bs-target="#productQuickView"
                            data-fullsize="http://relmotor.lan/wp-content/uploads/2024/07/91c91S9128.jpg"
                            onerror="this.onerror=null; this.src='/wp-content/uploads/2021/09/imagen-no-disponible.png';">
                    </td>
                    <td class="results-code"><a href="http://relmotor.lan/producto/inyector-12/">S9128</a></td>
                    <td class="d-none d-xl-table-cell product-cate">MOTOR DE PARTIDA</td>
                    <td class="d-none d-lg-table-cell results-description" style="width: 217.201px;">

                        <div class="mb-2 collapsible-description">
                            <strong>Descripción:</strong>
                            <div class="truncated-text">24V 4,5 KW - T11 G40 CW - RP30 - BOCA 105 - HINO 500 J05C, J...
                            </div>
                            <div class="collapse full-text" id="description-0">
                                <div class="mt-2">24V 4,5 KW - T11 G40 CW - RP30 - BOCA 105 - HINO 500 J05C, J08C,
                                    CAMIONES HINO FM2628, KOBELCO SK350</div>
                            </div>
                            <button class="btn btn-link btn-sm p-0 mt-1 toggle-text" type="button"
                                data-bs-toggle="collapse" data-bs-target="#description-0" aria-expanded="false"
                                aria-controls="description-0">
                                Mostrar +
                            </button>
                        </div>


                        <div class="mb-2 collapsible-description">
                            <strong>Referencia Plaza:</strong>
                            <div class="truncated-text">QUIROZ MPKMP1738, MPOT50737, ISIS MSW02, LUCAS MTHIT8, JN 41...
                            </div>
                            <div class="collapse full-text" id="short-description-0">
                                <div class="mt-2">QUIROZ MPKMP1738, MPOT50737, ISIS MSW02, LUCAS MTHIT8, JN 41055009, AS
                                    S9128, F042001003, HINO 281002326A, 281002327E, 281002622B, 281002623, 281002624,
                                    2810078063, SAWAFUJI 03505020217, 03505020512, 03555020012, 03555020013,
                                    03555020014, 03555020015, 03555020016, 03555020050, 03555020052, 03555020217, TOYOTA
                                    281002326, WAI 19956N, 281002625A, 03555020040, 03555020019, 03555020041,
                                    03505020253</div>
                            </div>
                            <button class="btn btn-link btn-sm p-0 mt-1 toggle-text" type="button"
                                data-bs-toggle="collapse" data-bs-target="#short-description-0" aria-expanded="false"
                                aria-controls="short-description-0">
                                Mostrar más
                            </button>
                        </div>

                    </td>
                    <td class="d-none d-xl-table-cell brand-cell">
                        <img src="http://relmotor.lan/wp-content/uploads/2024/10/logo-as-parts-oct-2024-sq-t-100x100.png"
                            alt="AS PARTS" class="brand-logo">
                    </td>
                    <td class="price_list">
                        <div class="results-price-list">Precio lista: <span class="results-price-value">$169.990</span>
                        </div>
                        <div class="results-price-discount">PRECIO CON DTO:</div>
                        <div class="results-price">$169.990 <small>Neto</small></div>
                    </td>
                    <td class="add2cart">
                        <div class="quantity-control d-flex align-items-center justify-content-between mb-2">
                            <button class="btn btn-outline-secondary input-quantity-btn" type="button"
                                data-action="decrease">-</button>
                            <input type="number" class="form-control input-quantity" value="0" min="0" max="22"
                                data-product-id="26653">
                            <button class="btn btn-outline-secondary input-quantity-btn" type="button"
                                data-action="increase">+</button>
                        </div>

                        <!-- Debo actualizar el badge con la cantidad en el carrito  agregada por updateStockDisplayFromCart() -->
                        <button class="btn btn-success w-100 mb-2 add-to-cart-btn" data-product-id="26653">
                            <i class="fas fa-cart-plus"></i> Añadir

                            <!-- badge -->
                            <span class="badge badge-light incart_counter invisible"
                                data-product-id="26653">&nbsp;&nbsp;&nbsp;</span>
                            <span class="sr-only">cant. en carrito</span>
                        </button>

                        <div class="stock-info" data-product-id="26653" data-original-stock="22"
                            data-current-stock="22"><span class="display_instock">STOCK: 22</span></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>



<?php

    $demo = (bool) ($_GET['demo'] ?? 0);
?>

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
                <th class="image-cell"><div class="th-content">IMÁGEN <img decoding="async" src="http://relmotor.lan/wp-content/plugins/relmotor-central/app/core/../../app/shortcodes/relmotor/assets/img/table-head-icons/imagen.svg" alt="Imagen icon" class="table-head-icon"></div></th>
                <th class="d-none d-lg-table-cell results-code"><div class="th-content">CÓDIGO <img decoding="async" src="http://relmotor.lan/wp-content/plugins/relmotor-central/app/core/../../app/shortcodes/relmotor/assets/img/table-head-icons/codigo.svg" alt="Código icon" class="table-head-icon"></div></th>
                <th class="d-none d-xl-table-cell product-cate"><div class="th-content">PRODUCTO <img decoding="async" src="http://relmotor.lan/wp-content/plugins/relmotor-central/app/core/../../app/shortcodes/relmotor/assets/img/table-head-icons/producto.svg" alt="Producto icon" class="table-head-icon"></div></th>
                <th class="d-none d-lg-table-cell results-description"><div class="th-content">DESCRIPCIÓN <img decoding="async" src="http://relmotor.lan/wp-content/plugins/relmotor-central/app/core/../../app/shortcodes/relmotor/assets/img/table-head-icons/descripcion.svg" alt="Descripción icon" class="table-head-icon"></div></th>
                <th class="d-none d-xl-table-cell brand-cell"><div class="th-content">MARCA <img decoding="async" src="http://relmotor.lan/wp-content/plugins/relmotor-central/app/core/../../app/shortcodes/relmotor/assets/img/table-head-icons/marca.svg" alt="Marca icon" class="table-head-icon"></div></th>
                <th class="price_list"><div class="th-content">PRECIO <img decoding="async" src="http://relmotor.lan/wp-content/plugins/relmotor-central/app/core/../../app/shortcodes/relmotor/assets/img/table-head-icons/precio.svg" alt="Precio icon" class="table-head-icon"></div></th>
                <th class="add2cart">COMPRAR</th>
            </tr>
            </thead>
            <tbody>
                <!-- Generado dinamicamente -->
                <?php 
                    if ($demo){
                        include __DIR__ . '/dummy_row.html';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>



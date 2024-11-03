<?php

    $att_names = array_column($atts, 'name');
    $att_keys  = array_column($atts, 'key');
?>

<script>
    const att_names = <?= json_encode($att_names) ?>;
    const att_keys  = <?= json_encode($att_keys) ?>;
    const user_id   = <?= $user_id ?? null ?>;
</script>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <!-- Spinner container -->
        <div id="spinner-container" style="display: none; position: absolute; top: 160px; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <img src="<?= shortcode_asset(__DIR__ . '/img/loading-2.gif') ?>" id="loading-image" width="60px" alt="Cargando...">
            </div>
        </div>
        
        <div class="col-lg-12 col-xl-10">
            <div class="search-form">
                <h2 class="mb-4">BÚSQUEDA GENERAL</h2>
                <form>
                    <!-- Barra de búsqueda principal -->
                    <div class="search-header mb-3">
                        <!-- Input de búsqueda -->
                        <div class="search-input-container">
                            <input type="search" 
                                class="form-control onmisearch-input" 
                                id="anything" 
                                placeholder="Código, aplicación o descripción" 
                                autocomplete="off">
                        </div>
                        
                        <!-- Contenedor del switch -->
                        <div class="switch-wrapper">
                            <label class="switch-label" style="margin-right:45px">Avanzada</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="advancedSearchToggle" 
                                    role="switch">
                            </div>
                        </div>
                    </div>

                    <!-- Sección colapsable -->
                    <div class="collapse" id="advancedSearchSection">
                        <div class="row">
                            <!-- Category -->
                            <div class="col-md-4 mb-3">                            
                                <div class="label-container">
                                    <label for="woo_cat_prd" class="form-label">Producto <span class="info-icon">i</span></label>
                                    <span class="status-light" id="status-light-cat_prd"></span>
                                </div>
                                <select name="woo_cat_prd[]" id="woo_cat_prd" data-placeholder="Categoría" class="form-control"></select>
                            </div>

                            <!-- Attributes -->
                            <?php foreach ($att_names as $ix => $att_name): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="label-container">
                                        <label for="<?= $att_keys[$ix] ?>" class="form-label"><?= $att_name ?> 
                                            <span class="info-icon">i</span>
                                        </label>
                                        <span class="status-light" id="status-light-att-<?= $att_keys[$ix] ?>"></span>
                                    </div>
                                    <select name="<?= $att_keys[$ix] ?>[]" data-id="<?= $att_keys[$ix] ?>" data-placeholder="<?= $att_name ?>" class="form-control woo_att"></select>
                                </div>
                            <?php endforeach; ?>
                        
                            <div class="mb-3">                    
                                <input type="search" class="form-control" id="buscar-codigo" placeholder="BUSCAR POR CÓDIGO">
                                <p class="help-text">Para buscar más de un SKU, separarlo por comas.</p>
                            </div>
                            
                            <div class="row mb-3">
                                <!-- Checkboxes en columna -->
                                <div class="checkbox-column mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="oferta">
                                        <label class="form-check-label" for="oferta">Oferta Especial</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="stock">
                                        <label class="form-check-label" for="stock">En stock</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary clearSearchForm">Limpiar</button>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div> -->

                    <div class="d-flex justify-content-center">
                        <div class="btn-group btn-group-pill" role="group" aria-label="Acciones de búsqueda">
                            <button type="button" class="btn clearSearchForm">LIMPIAR</button>
                            <button type="submit" class="btn btn-primary">BUSCAR</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- pagina de resultados -->
            <?php include __DIR__ . '/results.php'; ?>
        </div>
    </div>
</div>


<!-- MODAL DEMO -->
<?php 

$rol = $_GET['rol'] ?? 'comprador';

// if ($rol == 'comprador'){
//     include 'D:\www\relmotor\wp-content\plugins\relmotor-central\app\shortcodes\relmotor\views\quickview_customer.php'; 
// } else {
//     // vendedor
//     include 'D:\www\relmotor\wp-content\plugins\relmotor-central\app\shortcodes\relmotor\views\quickview_seller.php'; 

// }
?>

<script>
    $(document).ready(function() {
        // Inicializa Select2 en el select
        $('#woo_cat_prd').select2({
        placeholder: "Seleccionar",
        allowClear: true,
        data: [
            { id: 'al', text: 'Alternador' },
            { id: 're', text: 'Regulador' },
            { id: 'bc', text: 'Bocina' }
        ]
        });

        $('select[data-id="pa_sistema-electrico"]').select2({
        placeholder: "Seleccionar",
        allowClear: true,
        data: [
            { id: 'de', text: 'DENSO' },
            { id: 'iv', text: 'IVECO' },
            { id: 'fd', text: 'FORD' }
        ]
        });

        $('select[data-id="pa_marca"]').select2({
        placeholder: "Seleccionar",
        allowClear: true,
        data: [
            { id: 'dn', text: 'DNI' },
            { id: 'gs', text: 'GAUSS' },
            { id: 'ap', text: 'AS PARTS' }
        ]
        });
    });

    /*
        Form Expansion
    */
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('advancedSearchToggle');
        const advancedSection = document.getElementById('advancedSearchSection');
        
        toggle.addEventListener('change', function() {
            const bsCollapse = new bootstrap.Collapse(advancedSection, {
                toggle: false
            });
            
            if (this.checked) {
                bsCollapse.show();
            } else {
                bsCollapse.hide();
            }
        });

        // Recuperar el estado guardado
    const savedState = localStorage.getItem('advancedSearchState');
    
    // Si existe un estado guardado, aplicarlo
    if (savedState === 'true') {
        toggle.checked = true;
        // Mostrar la sección avanzada inmediatamente
        new bootstrap.Collapse(advancedSection, {
            toggle: false
        }).show();
    }
    
    // Escuchar cambios en el switch
    toggle.addEventListener('change', function() {
        const bsCollapse = new bootstrap.Collapse(advancedSection, {
            toggle: false
        });
        
        if (this.checked) {
            bsCollapse.show();
            // Guardar estado expandido
            localStorage.setItem('advancedSearchState', 'true');
        } else {
            bsCollapse.hide();
            // Guardar estado colapsado
            localStorage.setItem('advancedSearchState', 'false');
        }
    });
    });

    // $(document).ready(function () {
    //     $('#productQuickView').modal('show');
    // });
</script>
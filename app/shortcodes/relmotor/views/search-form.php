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
            <?php // include __DIR__ . '/results.php'; ?>
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
    // $(document).ready(function () {
    //     $('#productQuickView').modal('show');
    // });

    document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('advancedSearchToggle');
    const advancedSection = document.getElementById('advancedSearchSection');
    
    function checkFormValues() {
        let hasValues = false;
        let filledElements = [];

        // Revisar input de búsqueda por código
        const codigoInput = document.getElementById('buscar-codigo');
        if (codigoInput.value.trim()) {
            hasValues = true;
            filledElements.push('Código: ' + codigoInput.value);
        }

        // Revisar todos los select2
        const select2Elements = document.querySelectorAll('select');
        select2Elements.forEach(select => {
            const value = $(select).val();
            if (value && value.length) {
                hasValues = true;
                // Manejar tanto valores únicos como múltiples
                const selectedValue = Array.isArray(value) ? value.join(', ') : value;
                const placeholder = $(select).data('placeholder') || select.id;
                filledElements.push(placeholder + ': ' + selectedValue);
            }
        });

        // Revisar checkboxes
        const checkboxes = ['oferta', 'stock'];
        checkboxes.forEach(id => {
            const checkbox = document.getElementById(id);
            if (checkbox && checkbox.checked) {
                hasValues = true;
                filledElements.push('Checkbox: ' + checkbox.nextElementSibling.textContent.trim());
            }
        });

        return { hasValues, filledElements };
    }

    toggle.addEventListener('change', function() {
        if (!this.checked) {  // Si está intentando colapsar
            const { hasValues, filledElements } = checkFormValues();
            
            if (hasValues) {
                console.log('No se puede colapsar. Los siguientes campos tienen valores:');
                filledElements.forEach(element => console.log(element));
                
                // Prevenir el colapso
                this.checked = true;
                return;
            }
        }

        const bsCollapse = new bootstrap.Collapse(advancedSection, {
            toggle: false
        });
        
        if (this.checked) {
            bsCollapse.show();
            localStorage.setItem('advancedSearchState', 'true');
        } else {
            bsCollapse.hide();
            localStorage.setItem('advancedSearchState', 'false');
        }
    });

    // Recuperar estado guardado al cargar
    const savedState = localStorage.getItem('advancedSearchState');
    if (savedState === 'true') {
        toggle.checked = true;
        new bootstrap.Collapse(advancedSection, {
            toggle: false
        }).show();
    }
});
</script>
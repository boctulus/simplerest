<?php

$att_names = array_column($atts, 'name');
$att_keys = array_column($atts, 'key');
?>

<script>
    const att_names = <?= json_encode($att_names) ?>;
    const att_keys = <?= json_encode($att_keys) ?>;
    const user_id = <?= $user_id ?? null ?>;
</script>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <!-- Spinner container -->
        <div id="spinner-container"
            style="display: none; position: absolute; top: 160px; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 1000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <img src="<?= shortcode_asset(__DIR__ . '/img/loading-2.gif') ?>" id="loading-image" width="60px"
                    alt="Cargando...">
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
                            <input type="search" class="form-control onmisearch-input" id="anything"
                                placeholder="Código, aplicación o descripción" autocomplete="off">
                        </div>

                        <!-- Contenedor del switch -->
                        <div class="switch-wrapper">
                            <label class="switch-label" style="margin-right:45px">Avanzada</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="advancedSearchToggle" role="switch">
                            </div>
                        </div>
                    </div>

                    <!-- Sección colapsable -->
                    <div class="collapse" id="advancedSearchSection">
                        <div class="row">
                            <!-- Category -->
                            <div class="col-md-4 mb-3">
                                <div class="label-container">
                                    <label for="woo_cat_prd" class="form-label">Producto <span
                                            class="info-icon">i</span></label>
                                    <span class="status-light" id="status-light-cat_prd"></span>
                                </div>
                                <select name="woo_cat_prd[]" id="woo_cat_prd" data-placeholder="Categoría"
                                    class="form-control"></select>
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
                                    <select name="<?= $att_keys[$ix] ?>[]" data-id="<?= $att_keys[$ix] ?>"
                                        data-placeholder="<?= $att_name ?>" class="form-control woo_att"></select>
                                </div>
                            <?php endforeach; ?>

                            <div class="mb-3">
                                <input type="search" class="form-control" id="buscar-codigo"
                                    placeholder="BUSCAR POR CÓDIGO">
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
    // $(document).ready(function () {
    //     $('#productQuickView').modal('show');
    // });

    function highlightField($element) {
        // Identificar el tipo de elemento
        if ($element.is('span.select2')) {
            // Es un SELECT2
            let isOn = true;
            const interval = setInterval(() => {
                if (isOn) {
                    $element.css('border', '2px solid #ffa500');
                } else {
                    $element.css('border', '');
                }
                isOn = !isOn;
            }, 100);

            setTimeout(() => {
                clearInterval(interval);
                $element.css('border', '');
            }, 500);
        } 
        else if ($element.is('div.form-check')) {
            // Es un CHECKBOX
            // const $checkbox = $element.find('input[type="checkbox"]');
            // const originalBorderColor = $checkbox.css('border-color');
            // const originalBgColor = $checkbox.css('background-color');
            // const originalCheckedState = $checkbox.prop('checked');

            // // Cambiar a naranja
            // $checkbox.css({
            //     'border-color': '#ffa500',
            //     'background-color': '#ffa500'
            // });

            // // Parpadear el estado del checkbox
            // const flashTimes = 5;
            // let count = 0;
            // const interval = setInterval(() => {
            //     $checkbox.prop('checked', !$checkbox.prop('checked'));
            //     count++;
            //     if (count >= flashTimes * 2) {
            //         clearInterval(interval);
            //         // Restaurar colores originales
            //         $checkbox.css({
            //             'border-color': originalBorderColor,
            //             'background-color': originalBgColor
            //         });
            //         // Dejar el checkbox encendido
            //         $checkbox.prop('checked', true);
            //     }
            // }, 50);
        } 
        else {
            // Es un INPUT u otro elemento
            let isOn = true;
            const interval = setInterval(() => {
                if (isOn) {
                    $element.css('border', '2px solid #ffa500');
                } else {
                    $element.css('border', '');
                }
                isOn = !isOn;
            }, 100);

            setTimeout(() => {
                clearInterval(interval);
                $element.css('border', '');
            }, 500);
        }
    }

    function showToast(message) {
        toastr.info(message, '', {
            timeOut: 500,
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right"
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('advancedSearchToggle');
        const advancedSection = document.getElementById('advancedSearchSection');
        const select2Elements = document.querySelectorAll('select'); // Movido aquí
        const checkboxes = ['oferta', 'stock']; // Movido aquí también
        
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
            select2Elements.forEach(select => {
                const value = $(select).val();
                if (value && value.length) {
                    hasValues = true;
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

        // Modificar la parte del toggle.addEventListener
        toggle.addEventListener('change', function () {
            if (!this.checked) {  // Si está intentando colapsar
                const { hasValues, filledElements } = checkFormValues();

                if (hasValues) {
                    // Mostrar toast
                    showToast('Para colapsar el formulario, primero debe vaciar los campos con valores');

                    // Highlight de los campos con valores
                    select2Elements.forEach(select => {
                        const value = $(select).val();
                        if (value && value.length) {
                            highlightField($(select).next('.select2-container'));
                        }
                    });

                    const codigoInput = document.getElementById('buscar-codigo');
                    if (codigoInput.value.trim()) {
                        highlightField($(codigoInput));
                    }

                    checkboxes.forEach(id => {
                        const checkbox = document.getElementById(id);
                        if (checkbox && checkbox.checked) {
                            highlightField($(checkbox).parent());
                        }
                    });

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
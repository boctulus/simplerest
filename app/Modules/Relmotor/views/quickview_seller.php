<!-- MODAL -->
<div class="modal fade show" id="productQuickView" tabindex="-1" aria-labelledby="productQuickViewLabel"
    aria-modal="true" role="dialog" style="display: block;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-navbar">FICHA DE PRODUCTO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 mb-3">
                        <a href="http://relmotor.lan/producto/alternador-wai-12723n/"><img
                                src="http://relmotor.lan/wp-content/uploads/2024/04/7cd3012723N-350x350.jpg"
                                class="img-fluid w-100" id="modalImage" alt="ALTERNADOR WAI - 24V SCANIA 24V"></a>
                    </div>
                    <div class="col-md-6 p-3">
                        <h2 class="bold" id="modal-title">ALTERNADOR WAI - 24V SCANIA 24V BLA BLA</h2>
                        <table class="table table-borderless table-horizontal-lines" style="margin-left:-10px">
                            <tbody>
                                <tr>
                                    <th scope="row">Código:</th>
                                    <td><span id="modal-sku" class="featured-color"><a
                                                href="http://relmotor.lan/producto/alternador-wai-12723n/">12723N</a></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Producto</th>
                                    <td><span id="modal-category">ALTERNADOR</span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Marca</th>
                                    <td>WAI</td>
                                </tr>
                                <tr>
                                    <th scope="row">Sistema Eléctrico</th>
                                    <td>24V SCANIA</td>
                                </tr>
                            </tbody>
                        </table>

                        <p class="featured-color"><span id="modal-price">$144.990</span> Neto</p>

                        <!--p id="modal-stock" class="qv-display_outofstock">Sin existencias</p-->

                        <!--button type="button" class="btn btn-warning position-absolute bottom-0 end-0 m-3" onclick="jQuery('#productQuickView').printThis()">
                            <i class="fas fa-print"></i> Imprimir
                        </button-->
                        

                    </div>
                </div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button" role="tab" aria-controls="description"
                            aria-selected="true">Descripción</button>
                    </li>
                    <!-- Oculto el botón del tab "Información adicional" -->
                    <li class="nav-item" role="presentation" style="display: none;">
                        <button class="nav-link" id="additional-info-tab" data-bs-toggle="tab"
                            data-bs-target="#additional-info" type="button" role="tab" aria-controls="additional-info"
                            aria-selected="false">Información adicional</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="myTabContent">
                    <div class="tab-pane fade active show" id="description" role="tabpanel"
                        aria-labelledby="description-tab">
                        <h3 id="modal-description-title" class="bold">Descripción</h3>
                        <p id="modal-description-content">24V 110A - SCANIA 8PK SERIE 4 - P310 - K310, K340 -
                            0124655007, 0986047820, 1475569, 1763036, 1777464, REEMPLAZO PARA 0124555034, 0124655026,
                            ZM9020801</p>
                    </div>
                    <div class="tab-pane fade" id="additional-info" role="tabpanel"
                        aria-labelledby="additional-info-tab">
                        <h3 id="modal-additional-info-title" class="bold">Información adicional</h3>
                        <p id="modal-additional-info-content">A5281, 0124655026, 12723N, A0064, AVI1440805</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.key === 'i') {
        event.preventDefault(); // Previene el comportamiento predeterminado de Ctrl+i
        
        var additionalInfoTab = document.querySelector('#myTab .nav-item:nth-child(2)');
        var additionalInfoContent = document.getElementById('additional-info');
        
        if (additionalInfoTab.style.display === 'none') {
            additionalInfoTab.style.display = '';
            additionalInfoContent.classList.add('active', 'show');
            document.getElementById('additional-info-tab').classList.add('active');
            
            document.getElementById('description').classList.remove('active', 'show');
            document.getElementById('description-tab').classList.remove('active');
        } else {
            additionalInfoTab.style.display = 'none';
            additionalInfoContent.classList.remove('active', 'show');
            document.getElementById('additional-info-tab').classList.remove('active');
            
            document.getElementById('description').classList.add('active', 'show');
            document.getElementById('description-tab').classList.add('active');
        }
    }
});
</script>   
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-sm-6 col-md-8 col-lg-12 col-xl-12">
            <div class="search-form">
                <h2 class="mb-4">CÓDIGO, APLICACIÓN O DESCRIPCIÓN</h2>
                <form>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="codigo" placeholder="ALT9010104">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="producto" class="form-label">Producto <span class="info-icon">i</span></label>
                            <input type="text" class="form-control" id="producto" placeholder="Producto">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sistema" class="form-label">Sistema Eléctrico <span class="info-icon">i</span></label>
                            <input type="text" class="form-control" id="sistema" placeholder="Sistema Eléctrico">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="marca" class="form-label">Marca <span class="info-icon">i</span></label>
                            <input type="text" class="form-control" id="marca" placeholder="Marca">
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="buscar-codigo" placeholder="BUSCAR POR CODIGO">
                        <p class="help-text">Para buscar más de un SKU, separarlo por comas.</p>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="oferta" checked>
                                <label class="form-check-label" for="oferta">En oferta</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="stock">
                                <label class="form-check-label" for="stock">En stock</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary">Limpiar</button>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- pagina de resultados -->
            <?= include __DIR__ . '/results.php' ?>
        </div>
    </div>
</div>

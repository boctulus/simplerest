<script>
    const att_names = <?= json_encode($atts) ?>;
</script>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-10">
            <div class="search-form">
                <h2 class="mb-4">CÓDIGO, APLICACIÓN O DESCRIPCIÓN</h2>
                <form>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="codigo" placeholder="ALT9010104">
                    </div>
                    <div class="row">
                        <!-- Category -->
                        <div class="col-md-4 mb-3">
                            <label for="producto" class="form-label">Producto <span class="info-icon">i</span></label>
                            <select name="producto[]" id="producto" placeholder="Categoría" class="form-control">
                                <option value="">--</option>
                            </select>
                        </div>

                        <!-- Attributes -->
                        <?php foreach ($atts as $att): ?>
                            <?php 
                                // Convert attribute to snake_case for the select name and id
                                $name = strtolower(str_replace(' ', '_', $att)); 
                            ?>
                            <div class="col-md-4 mb-3">
                                <label for="<?= $name ?>" class="form-label"><?= $att ?> <span class="info-icon">i</span></label>
                                <select name="<?= $name ?>[]" data-id="<?= $att ?>" placeholder="<?= $att ?>" class="form-control">
                                    <option value="">--</option>
                                </select>
                            </div>
                        <?php endforeach; ?>
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
            <?php include __DIR__ . '/results.php'; ?>

        </div>
    </div>
</div>

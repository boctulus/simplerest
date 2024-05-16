<div class="container mt-4">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="ejecutar-orden-tab" data-bs-toggle="tab" data-bs-target="#ejecutar-orden" type="button" role="tab" aria-controls="ejecutar-orden" aria-selected="true">Ejecutar Orden</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="resultado-tab" data-bs-toggle="tab" data-bs-target="#resultado" type="button" role="tab" aria-controls="resultado" aria-selected="false">Resultado</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="ejecutar-orden" role="tabpanel" aria-labelledby="ejecutar-orden-tab">
            <div class="mt-3">
                <textarea class="form-control" id="jsonInput" rows="10" placeholder="Ingresa JSON aquí"></textarea>
                <button class="btn btn-primary mt-2 float-end" id="sendJsonBtn">Enviar</button>
            </div>
        </div>
        <div class="tab-pane fade" id="resultado" role="tabpanel" aria-labelledby="resultado-tab">
            <div class="mt-3 row">
                <div class="col-md-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Fecha y Hora de Ejecución</th>
                                <th scope="col">Archivo de Orden</th>
                                <th scope="col">Estado del Robot</th>
                                <th scope="col">Mensaje de Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí puedes agregar dinámicamente las filas de la tabla con los datos -->
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <img src="image_placeholder.png" alt="Resultado" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
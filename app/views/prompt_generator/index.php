<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Generador de Prompt</h1>

            <!-- Sección de introducción -->
            <div class="mb-3">
                <label for="promptIntro" class="form-label">PROMPT Introducción</label>
                <textarea class="form-control" id="promptIntro" rows="4"
                    placeholder="Escribe el texto de introducción..."></textarea>
            </div>

            <!-- Sección para las rutas de los archivos -->
            <div class="mb-3">
                <label class="form-label">RUTAS A ARCHIVOS A INCLUIR</label>
                <div id="filePathsContainer">
                    <!-- Los inputs de rutas se agregarán aquí dinámicamente -->
                </div>
                <button class="btn btn-success mt-2" id="addFilePath">Agregar ruta</button>
                <button class="btn btn-danger mt-2 float-end" id="deleteSelectedPaths">Borrar rutas
                    seleccionadas</button>
            </div>

            <!-- Sección de notas finales -->
            <div class="mb-3">
                <label for="promptFinal" class="form-label">PROMPT (Notas finales)</label>
                <input type="text" class="form-control" id="promptFinal" placeholder="Escribe las notas finales...">
            </div>

            <!-- Botón para generar el prompt -->
            <button class="btn btn-primary" id="generatePrompt">Generar Prompt</button>

            <!-- Sección donde se muestra el prompt generado con el botón de copiar -->
            <div class="position-relative mt-4">
                <label for="generatedPrompt" class="form-label">PROMPT GENERADO</label>
                <button class="btn btn-light border" id="copyPrompt" title="Copiar al portapapeles">
                    <img src="<?= asset('img/copy-icon.svg') ?>" alt="Copiar" width="30" height="30">
                </button>
                <textarea class="form-control" id="generatedPrompt" rows="6" readonly></textarea>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){
    function addFilePathInput() {
        let inputHtml = `
            <div class="input-group mb-2 file-path-group">
                <div class="input-group-text">
                    <input type="checkbox" class="form-check-input mt-0 file-path-checkbox">
                </div>
                <input type="text" class="form-control file-path-input" placeholder="Ingresa la ruta del archivo...">
                <button class="btn btn-outline-secondary delete-file-path" type="button">&times;</button>
            </div>
        `;
        $('#filePathsContainer').append(inputHtml);
    }

    addFilePathInput();

    $('#addFilePath').click(function() {
        addFilePathInput();
    });

    $(document).on('click', '.delete-file-path', function() {
        let $filePathGroup = $(this).closest('.file-path-group');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¿Quieres eliminar esta ruta de archivo?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $filePathGroup.remove();
                Swal.fire(
                    'Eliminado',
                    'La ruta ha sido eliminada.',
                    'success'
                )
            }
        })
    });

    $('#deleteSelectedPaths').click(function() {
        let $selectedPaths = $('.file-path-checkbox:checked').closest('.file-path-group');
        if ($selectedPaths.length === 0) {
            Swal.fire('Advertencia', 'No hay rutas seleccionadas para eliminar', 'warning');
            return;
        }
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Quieres eliminar ${$selectedPaths.length} ruta(s) seleccionada(s)?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $selectedPaths.remove();
                Swal.fire(
                    'Eliminado',
                    'Las rutas seleccionadas han sido eliminadas.',
                    'success'
                )
            }
        })
    });

    $('#generatePrompt').click(function(){
        // ... (código de generación de prompt, sin cambios)
    });

    $('#copyPrompt').click(function(){
        // ... (código de copia al portapapeles, sin cambios)
    });
});
</script>

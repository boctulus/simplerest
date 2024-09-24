<style>
    /* Posicionar el botón de copiar en el margen superior derecho del textarea */
    #copyPrompt {
        position: absolute;
        top: 0;
        right: 0;
        margin: 32px 0 0 0;
        z-index: 10;
    }
    .position-relative {
        position: relative;
    }
</style>

<div class="container mt-4">
        <h2 class="mb-3">Generador de Prompt</h2>
        
        <!-- Sección de introducción -->
        <div class="mb-3">
            <label for="promptIntro" class="form-label">PROMPT Introducción</label>
            <textarea class="form-control" id="promptIntro" rows="4" placeholder="Escribe el texto de introducción..."></textarea>
        </div>
        
        <!-- Sección para las rutas de los archivos -->
        <div class="mb-3">
            <label for="filePaths" class="form-label">RUTAS A ARCHIVOS A INCLUIR</label>
            <textarea class="form-control" id="filePaths" rows="3" placeholder="Ingresa las rutas de los archivos, una por línea..."></textarea>
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
            <!-- Botón de copiar con ícono en el margen superior derecho -->
            <button class="btn btn-light border" id="copyPrompt" title="Copiar al portapapeles">
                <!-- Icono SVG de copiar -->
                <svg aria-hidden="true" focusable="false" role="img" class="octicon octicon-copy" viewBox="0 0 16 16" width="16" height="16" fill="currentColor" style="display: inline-block; user-select: none; vertical-align: text-bottom; overflow: visible;"><path d="M0 6.75C0 5.784.784 5 1.75 5h1.5a.75.75 0 0 1 0 1.5h-1.5a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 9.25 16h-7.5A1.75 1.75 0 0 1 0 14.25Z"></path><path d="M5 1.75C5 .784 5.784 0 6.75 0h7.5C15.216 0 16 .784 16 1.75v7.5A1.75 1.75 0 0 1 14.25 11h-7.5A1.75 1.75 0 0 1 5 9.25Zm1.75-.25a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25Z"></path></svg>
            </button>
            <textarea class="form-control" id="generatedPrompt" rows="6" readonly></textarea>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#generatePrompt').click(function(){
                // Obtener valores de los campos
                let intro = $('#promptIntro').val();
                let filePaths = $('#filePaths').val().split('\n');
                let finalNotes = $('#promptFinal').val();
                
                // Generar el prompt concatenado
                let generatedPrompt = intro + '\n\n';
                
                // Agregar archivos con cabeceras
                filePaths.forEach(function(path) {
                    if(path.trim() !== '') {
                        generatedPrompt += `### Archivo: ${path.trim()} ###\n` + `/* Contenido del archivo ${path.trim()} */\n\n`;
                    }
                });
                
                // Agregar notas finales
                generatedPrompt += finalNotes;
                
                // Mostrar en el textarea
                $('#generatedPrompt').val(generatedPrompt);
            });

            // Copiar al portapapeles
            $('#copyPrompt').click(function(){
                let generatedPrompt = $('#generatedPrompt');
                generatedPrompt.select();
                document.execCommand('copy');
            });
        });
    </script>
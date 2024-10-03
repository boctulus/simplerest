<div class="container-fluid mt-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-3">Generador de Prompt</h1>

            <!-- Sección de introducción -->
            <div class="mb-5 position-relative">
                <label for="prompt-description" class="form-label">PROMPT Introducción</label>
                <textarea class="form-control" id="prompt-description" rows="4"
                    placeholder="Escribe el texto de introducción..."></textarea>
                <button class="btn btn-danger position-absolute end-0 mt-3" id="clearFormButton">Borrar Form</button>
            </div>

            <!-- Sección para las rutas de los archivos -->
            <div class="mb-3">
                <label class="form-label">RUTAS A ARCHIVOS A INCLUIR</label>
                <div id="filePathsContainer"></div>
                <button class="btn btn-success mt-2" id="addFilePath">Agregar ruta</button>

                <div class="btn-group mt-2 float-end">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Acción masiva
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                        <li><a class="dropdown-item" href="#" id="deleteSelectedPaths">Borrar rutas</a></li>
                        <li><a class="dropdown-item" href="#" id="enableSelectedPaths">Habilitar</a></li>
                        <li><a class="dropdown-item" href="#" id="disableSelectedPaths">Deshabilitar</a></li>
                    </ul>
                </div>
            </div>

            <!-- Sección de notas finales -->
            <div class="mb-3">
                <label for="promptFinal" class="form-label">PROMPT (Notas finales)</label>
                <textarea class="form-control" id="promptFinal" rows="4"
                    placeholder="Escribe las notas finales..."></textarea>
            </div>

            <!-- Botones para generar el prompt y ejecutar con opciones -->
            <div class="d-flex justify-content-between">
                <button id="generate-prompt" class="btn btn-primary">Generar Prompt</button>

                <!-- Botón dropdown para ejecutar con ChatGPT o Claude -->
                <div class="btn-group">
                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="executeButton">
                        Ejecutar con
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" id="executeWithChatGPT">ChatGPT</a></li>
                        <li><a class="dropdown-item" href="#" id="executeWithClaude">Claude</a></li>
                    </ul>
                </div>
            </div>

            <!-- Sección donde se muestra el prompt generado con el botón de copiar y el menú de opciones -->
            <div class="position-relative mt-4 mb-5">
                <label for="generatedPrompt" class="form-label">PROMPT GENERADO</label>
                <div class="dropdown float-end">
                    <button class="btn btn-light border dropdown-toggle" type="button" id="promptOptionsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="promptOptionsDropdown">
                        <li><a class="dropdown-item" href="#" id="fullscreenPrompt">Pantalla completa</a></li>
                        <li><a class="dropdown-item" href="#" id="copyPrompt">Copiar a portapapeles</a></li>
                    </ul>
                </div>
                <!--button class="btn btn-light border" id="copyPrompt" title="Copiar al portapapeles">
                    <img src="< ?= asset('img/copy-icon.svg') ? >" alt="Copiar" width="30" height="30">
                </button-->
                <textarea class="form-control" id="generatedPrompt" rows="6"></textarea>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para obtener el contenido desde la API
    function getPromptContent() {
        const description = $('#prompt-description').val();  // Introducción
        const files = $('.file-input').filter(function () {
            // Solo incluir rutas no vacías y no deshabilitadas
            return !$(this).closest('.file-path-group').attr('data-disabled') && $(this).val().trim() !== '';
        }).map(function () {
            return $(this).val().trim();
        }).get();

        // Preseguir y hacer la solicitud POST solo si hay archivos para procesar
        if (files.length == 0) {
            Swal.fire('Advertencia', 'Generando.... pero no hay rutas de archivos para procesar.', 'warning');
        }

        const notes = $('#promptFinal').val();
        // Obtener todas las rutas de archivos
        $('.file-input').not(':disabled').each(function () {
            const filePath = $(this).val();
            if (filePath) {
                files.push(filePath);
            }
        });

        const data = {
            description: description,
            files: files,
            notes: notes
        };

        // Hacer la solicitud POST
        $.ajax({
            url: 'http://simplerest.lan/api/v1/prompts',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (response) {
                // Procesar y mostrar el contenido recibido si es exitoso
                clearValidationErrors();  // Limpiar cualquier error anterior
                displayFileContents(description, response.data.prompts.content, files, notes);

                const id = response.data.id;
                const newUrl = `${window.location.pathname}#chat-${id}`;
                history.pushState({ id: id }, '', newUrl);
                saveFormVersion(id, description, files, notes);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 400) {
                    // Si el error es de validación, mostrarlo en el formulario
                    const validationErrors = xhr.responseJSON.error.detail;
                    showValidationErrors(validationErrors);
                } else {
                    console.error('Error al obtener el contenido del prompt:', error);
                }

                if (xhr.status === 500) {
                    const errorMessage = xhr.responseJSON.error.message;
                    const errorPath = errorMessage.match(/Path '(.+)' does not exist/)[1];

                    // Actualizar el input correspondiente a la ruta problemática
                    $('.file-input').each(function () {
                        if ($(this).val() === errorPath) {
                            let $group = $(this).closest('.file-path-group');
                            $group.replaceWith(addFilePathInput(errorPath, true));
                        }
                    });

                    // Mostrar el mensaje de error con SweetAlert
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    console.error('Error al obtener el contenido del prompt:', error);
                }
            }
        });

    }

    // Función para mostrar errores de validación
    function showValidationErrors(errors) {
        clearValidationErrors(); // Limpiar los errores anteriores

        // Iterar sobre los errores y asignarlos a los campos
        if (errors.description) {
            $('#prompt-description').addClass('is-invalid');
            $('#prompt-description').after(`<div class="invalid-feedback">${errors.description[0].error_detail}</div>`);
        }

        if (errors.files) {
            // Aquí podrías iterar sobre las rutas de archivos si es necesario
            $('.file-input').each(function (index) {
                if (errors.files[index]) {
                    $(this).addClass('is-invalid');
                    $(this).after(`<div class="invalid-feedback">${errors.files[index].error_detail}</div>`);
                }
            });
        }

        if (errors.notes) {
            $('#promptFinal').addClass('is-invalid');
            $('#promptFinal').after(`<div class="invalid-feedback">${errors.notes[0].error_detail}</div>`);
        }
    }

    function handleServerResponse(response) {
        if (response.error) {
            // Manejar errores de ruta
            $('.file-input').each(function (index) {
                let $input = $(this);
                let value = $input.val();
                let hasError = response.error.invalidPaths.includes(value);

                // Reemplazar el input actual con uno nuevo basado en si tiene error o no
                let $newInput = $(addFilePathInput(value, hasError));
                $input.closest('.file-path-group').replaceWith($newInput);
            });
        } else {
            // Limpiar errores si la respuesta es exitosa
            clearValidationErrors();
        }
    }

    // Función para agregar un input dinámico
    function addFilePathInput(value = '', hasError = false, isDisabled = false) {
        if (typeof value === 'object' && value !== null) {
            // Si es un objeto, intentamos obtener una propiedad que pueda contener la ruta
            value = '';
        }

        let inputHtml = `
            <div class="input-group mb-2 file-path-group" ${isDisabled ? 'data-disabled="true"' : ''}>
                <div class="input-group-text">
                    <input type="checkbox" class="form-check-input mt-0 file-path-checkbox" ${isDisabled ? 'checked' : ''}>
                </div>
                <input type="text" class="form-control file-input ${hasError ? 'is-invalid' : ''} ${isDisabled ? 'text-muted' : ''}" 
                    placeholder="Ingresa la ruta del archivo..." value="${value}" ${isDisabled ? 'disabled' : ''}>
                <button class="btn btn-outline-secondary delete-file-path" type="button">&times;</button>
                ${hasError ? `<div class="invalid-feedback">La ruta '${value}' no existe</div>` : ''}
            </div>
        `;
        $('#filePathsContainer').append(inputHtml);
    }

    function restoreDeleteButtons() {
        $('.file-path-group.has-error').each(function () {
            let $group = $(this);
            $group.removeClass('has-error');
            $group.find('.is-invalid').removeClass('is-invalid');
            $group.find('.invalid-feedback').remove();
            $group.append('<button class="btn btn-outline-secondary delete-file-path" type="button">&times;</button>');
        });
    }

    // Función para limpiar los errores de validación anteriores
    function clearValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        $('.file-path-group').each(function () {
            let $group = $(this);
            let value = $group.find('.file-input').val();
            let isDisabled = $group.attr('data-disabled') === 'true';
            $group.replaceWith(addFilePathInput(value, false, isDisabled));
        });
    }

    function clearForm() {
        $('#prompt-description').val('');
        $('#promptFinal').val('');
        $('#generatedPrompt').val('');

        // Vaciar el contenedor de rutas pero mantener un solo campo vacío
        $('#filePathsContainer').html(`
            <div class="input-group mb-2 file-path-group">
                <div class="input-group-text">
                    <input type="checkbox" class="form-check-input mt-0 file-path-checkbox">
                </div>
                <input type="text" class="form-control file-input" placeholder="Ingresa la ruta del archivo...">
                <button class="btn btn-outline-secondary delete-file-path" type="button">&times;</button>
            </div>
        `);

        restoreDeleteButtons();

        localStorage.removeItem('currentForm');
    }

    function saveFormVersion(id, description, files, notes) {
        const formVersion = {
            id: id,
            description: description,
            files: files.map(file => {
                if (typeof file === 'string') {
                    return {
                        path: file,
                        disabled: $('.file-input[value="' + file + '"]').closest('.file-path-group').attr('data-disabled') === 'true'
                    };
                }
                return file;
            }),
            notes: notes,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem(`chat-${id}`, JSON.stringify(formVersion));
    }

    function saveFormToLocalStorage() {
        const formState = {
            description: $('#prompt-description').val(),
            files: $('.file-input').map((_, input) => {
                const $input = $(input);
                const $group = $input.closest('.file-path-group');
                return {
                    path: $input.val(),
                    disabled: $group.attr('data-disabled') === 'true'
                };
            }).get(),
            notes: $('#promptFinal').val()
        };
        localStorage.setItem('currentForm', JSON.stringify(formState));
    }

    function loadFormStateFromLocalStorage() {
        const currentHash = window.location.hash;
        let savedForm;

        if (currentHash.startsWith('#chat-')) {
            const formId = currentHash.split('-')[1];
            savedForm = JSON.parse(localStorage.getItem(`chat-${formId}`));
        } else {
            savedForm = JSON.parse(localStorage.getItem('currentForm'));
        }

        if (savedForm) {
            $('#prompt-description').val(savedForm.description || '');
            $('#promptFinal').val(savedForm.notes || '');
            $('#filePathsContainer').empty(); // Limpiar contenedor existente

            if (Array.isArray(savedForm.files)) {
                // Usar un Set para evitar duplicados
                const uniquePaths = new Set();
                
                savedForm.files.forEach(file => {
                    let filePath, isDisabled;
                    if (typeof file === 'string') {
                        filePath = file;
                        isDisabled = false;
                    } else {
                        filePath = file.path;
                        isDisabled = file.disabled;
                    }
                    
                    // Solo agregar si la ruta no existe ya
                    if (!uniquePaths.has(filePath)) {
                        uniquePaths.add(filePath);
                        addFilePathInput(filePath, false, isDisabled);
                    }
                });
            }
        }

        // Asegurarse de que siempre haya al menos una ruta
        if ($('.file-path-group').length === 0) {
            addFilePathInput();
        }
    }

    /*
        Mostrar resultado en #generatedPrompt
    */
    function displayFileContents(description, contents, files, notes) {
        let generatedPrompt = '';

        // Incluir el contenido de la introducción
        generatedPrompt += `${description}\n\n`;

        // Iterar sobre los archivos y sus contenidos
        files.forEach(function (filePath, index) {
            let content = contents[index];
            let extension = filePath.split('.').pop();
            let $existingInput = $(`.file-input[value="${filePath}"]`);
            let isDisabled = $existingInput.closest('.file-path-group').attr('data-disabled') === 'true';

            // Formatear según la extensión del archivo
            let formattedContent;
            if (extension === 'css') {
                formattedContent = `\n/* Ruta: ${filePath} */\n\`\`\`css\n${content}\n\`\`\`\n`;
            } else if (extension === 'js') {
                formattedContent = `\n/* Ruta: ${filePath} */\n\`\`\`javascript\n${content}\n\`\`\`\n`;
            } else if (extension === 'php') {
                formattedContent = `\n/* Ruta: ${filePath} */\n\`\`\`php\n${content}\n\`\`\`\n`;
            } else if (extension === 'json' || extension === 'jsonl') {
                formattedContent = `\n/* Ruta: ${filePath} */\n\`\`\`json\n${content}\n\`\`\`\n`;
            } else {
                formattedContent = `\n/* Ruta: ${filePath} */\n\`\`\`\n${content}\n\`\`\`\n`;
            }

            // Agregar el contenido formateado al prompt
            generatedPrompt += formattedContent;
        });

        // Incluir las notas finales
        generatedPrompt += `\n${notes}\n`;

        // Mostrar el contenido en el textarea #generatedPrompt
        $('#generatedPrompt').val(generatedPrompt);
    }

    function handleHashChange() {
        const hash = window.location.hash;
        if (hash.startsWith('#chat-')) {
            loadFormStateFromLocalStorage();
        }
    }

    // Función para alternar pantalla completa
    function toggleFullscreen(element) {
        if (!document.fullscreenElement) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) { // Firefox
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { // IE/Edge
                element.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }
        }
    }

    function togglePathStatus(enable) {
        $('.file-path-checkbox:checked').each(function () {
            let $group = $(this).closest('.file-path-group');
            let $input = $group.find('.file-input');
            $group.attr('data-disabled', !enable);
            $input.prop('disabled', !enable);
            if (enable) {
                $group.removeClass('disabled');
                $input.removeClass('text-muted');
            } else {
                $group.addClass('disabled');
                $input.addClass('text-muted');
            }
        });
        updateBulkActionOptions();
    }

    function updateBulkActionOptions() {
        let $selectedPaths = $('.file-path-checkbox:checked');
        let allEnabled = $selectedPaths.closest('.file-path-group').not('.disabled').length === $selectedPaths.length;
        let allDisabled = $selectedPaths.closest('.file-path-group.disabled').length === $selectedPaths.length;

        $('#enableSelectedPaths').toggle(!allEnabled);
        $('#disableSelectedPaths').toggle(!allDisabled);
    }

    $('#enableSelectedPaths').click(function (e) {
        e.preventDefault();
        togglePathStatus(true);
    });

    $('#disableSelectedPaths').click(function (e) {
        e.preventDefault();
        togglePathStatus(false);
    });

    // Llamar a esta función cuando se seleccionen/deseleccionen rutas
    $(document).on('change', '.file-path-checkbox', updateBulkActionOptions);

    // Manejar el evento de carga de la página
    window.onload = function () {
        // Si la página carga con un hash en la URL
        if (window.location.hash) {
            handleHashChange();
        }
    };

    // Manejar el evento 'hashchange' para detectar cambios en el hash de la URL
    window.addEventListener('hashchange', handleHashChange);

    window.addEventListener('popstate', function (event) {
        if (event.state && event.state.id) {
            loadFormStateFromLocalStorage();
        }
    });

    // Manejar el estado del historial para cuando se navega hacia atrás o adelante
    window.onpopstate = function () {
        handleHashChange();
    };


    $(document).ready(function () {
        // Inicializar eventos
        initializeEvents();
        addFilePathInput(); // que haya al menos una ruta

        // Función para inicializar los eventos
        function initializeEvents() {
            $('#addFilePath').click(function (e) {
                e.preventDefault();
                addFilePathInput();
            });

            $('#generatePrompt').click(function (e) {
                e.preventDefault();
                generatePrompt();
            });

            $('#deleteSelectedPaths').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                deleteSelectedPaths();
            });

            $('#deleteSelectedPaths').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                deleteSelectedPaths();
            });

            $('#enableSelectedPaths').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                togglePathStatus(true);
            });

            // Función para copiar al portapapeles
            $('#copyPrompt').click(function (e) {
                e.preventDefault();
                copyToClipboard();
            });

            $('#clearFormButton').click(clearForm);

            // Función para mostrar en pantalla completa
            $('#fullscreenPrompt').click(function (e) {
                e.preventDefault();
                toggleFullscreen($('#generatedPrompt')[0]);
            });

            // Asignar eventos a los botones del dropdown
            $('#executeWithChatGPT').click(function (e) {
                e.preventDefault();
                executeWithChatGPT();
            });

            $('#executeWithClaude').click(function (e) {
                e.preventDefault();
                executeWithClaude();
            });

            // Registrar el evento para generar el prompt
            $('#generate-prompt').on('click', function (e) {
                e.preventDefault();
                getPromptContent();
            });

            // Eventos para los botones de eliminar en inputs dinámicos
            $(document).on('click', '.delete-file-path', function () {
                let $filePathGroup = $(this).closest('.file-path-group');
                deleteFilePath($filePathGroup);
            });

            $('form').on('submit', function (e) {
                e.preventDefault();
            });
        }

        // Función para eliminar rutas seleccionadas
        function deleteSelectedPaths() {
            let $selectedPaths = $('.file-path-checkbox:checked').closest('.file-path-group').not('.disabled');

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
                    );
                }
            });
        }

        // Función para eliminar una ruta específica
        function deleteFilePath($filePathGroup) {
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
                    );
                }
            });
        }

        // Función para generar el prompt
        function generatePrompt() {
            // Obtener valores de los campos
            let intro = $('#prompt-description').val();
            let finalNotes = $('#promptFinal').val();

            // Obtener las rutas de los inputs dinámicos
            let filePaths = $('.file-input').map(function() {
                let $input = $(this);
                let $group = $input.closest('.file-path-group');
                return {
                    path: $input.val().trim(),
                    disabled: $group.attr('data-disabled') === 'true'
                };
            }).get().filter(file => file.path !== '');

            // Generar el prompt concatenado
            let generatedPrompt = intro + '\n\n';

            // Agregar archivos con cabeceras
            filePaths.forEach(function (path) {
                generatedPrompt += `### Archivo: ${path} ###\n/* Contenido del archivo ${path} */\n\n`;
            });

            // Agregar notas finales
            generatedPrompt += finalNotes;

            // Mostrar en el textarea
            $('#generatedPrompt').val(generatedPrompt);
        }

        // Función para copiar el prompt generado al portapapeles sin seleccionar el texto
        function copyToClipboard() {
            let generatedPrompt = $('#generatedPrompt').val();

            // Verificar si la API del portapapeles está disponible
            if (navigator.clipboard && navigator.clipboard.writeText) {
                // Usamos el API del portapapeles si está disponible
                navigator.clipboard.writeText(generatedPrompt).then(function () {
                    // Si se copia correctamente, mostramos un mensaje de éxito
                    Swal.fire('Éxito', 'El prompt ha sido copiado al portapapeles', 'success');
                }).catch(function (error) {
                    // Si ocurre algún error, mostramos un mensaje de error
                    Swal.fire('Error', 'Hubo un problema al copiar el prompt', 'error');
                    console.error('Error al copiar al portapapeles:', error);
                });
            } else {
                // Fallback manual si la API del portapapeles no está disponible
                let tempTextArea = document.createElement("textarea");
                tempTextArea.value = generatedPrompt;
                document.body.appendChild(tempTextArea);

                // Seleccionamos el contenido del textarea temporal y lo copiamos
                tempTextArea.select();
                try {
                    document.execCommand("copy");  // Copiamos el texto seleccionado
                    Swal.fire('Éxito', 'El prompt ha sido copiado al portapapeles', 'success');
                } catch (error) {
                    Swal.fire('Error', 'Hubo un problema al copiar el prompt', 'error');
                    console.error('Error al copiar manualmente:', error);
                }

                // Limpiar el textarea temporal
                document.body.removeChild(tempTextArea);
            }
        }



        // Función para actualizar el texto del botón con la opción seleccionada
        function updateExecuteButtonText(optionText) {
            $('#executeButton').text('Ejecutar con ' + optionText);
        }

        // Función para ejecutar con ChatGPT
        function executeWithChatGPT() {
            let generatedPrompt = $('#generatedPrompt').val();
            console.log("Ejecutar con ChatGPT:", generatedPrompt);
            updateExecuteButtonText("ChatGPT");
            // Aquí puedes añadir la lógica para enviar el prompt a ChatGPT
        }

        // Función para ejecutar con Claude
        function executeWithClaude() {
            let generatedPrompt = $('#generatedPrompt').val();
            console.log("Ejecutar con Claude:", generatedPrompt);
            updateExecuteButtonText("Claude");
            // Aquí puedes añadir la lógica para enviar el prompt a Claude
        }


        // Función para cargar el formulario basado en el ID
        function loadFormFromLocalStorage(id) {
            // Recuperar el contenido del formulario del localStorage
            const formContent = localStorage.getItem(id);

            if (formContent) {
                // Mostrar el formulario en el contenedor
                document.getElementById('form-container').innerHTML = formContent;
            } else {
                // Si no hay formulario en localStorage para ese ID
                document.getElementById('form-container').innerHTML = `<p>No form found for ID: ${id}</p>`;
            }
        }




    });
</script>
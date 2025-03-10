// Functions for UI interactions and manipulations

function loadExternalLibraries() {
    // Cargar SweetAlert2 si no está ya cargada
    if (typeof Swal === 'undefined') {
        const sweetAlertScript = document.createElement('script');
        sweetAlertScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(sweetAlertScript);
    }

    // Cargar Toastr si no está ya cargada
    if (typeof toastr === 'undefined') {
        // Cargar CSS de Toastr
        const toastrCss = document.createElement('link');
        toastrCss.rel = 'stylesheet';
        toastrCss.href = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css';
        document.head.appendChild(toastrCss);

        // Cargar jQuery (requerido por Toastr)
        const jqueryScript = document.createElement('script');
        jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        jqueryScript.onload = function() {
            // Cargar Toastr después de jQuery
            const toastrScript = document.createElement('script');
            toastrScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js';
            toastrScript.onload = function() {
                // Configurar Toastr
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 3000
                }
            };
            document.head.appendChild(toastrScript);
        };
        document.head.appendChild(jqueryScript);
    }
}

function searchPromptsImpl(context) {
    if (!context.searchQuery.trim()) {
        context.searchResults = [];
        return;
    }

    // Mostrar el loader
    context.loading = true;

    // Simular una búsqueda con delay
    setTimeout(() => {
        const query = context.searchQuery.toLowerCase();
        const allPrompts = context.getSavedPrompts();

        context.searchResults = allPrompts.filter(prompt => {
            const description = (prompt.description || '').toLowerCase();
            const notes = (prompt.notes || '').toLowerCase();
            const paths = prompt.filePaths ? prompt.filePaths.map(f => f.path.toLowerCase()).join(' ') : '';

            return description.includes(query) ||
                notes.includes(query) ||
                paths.includes(query);
        });

        // Ocultar el loader
        context.loading = false;

        if (context.searchResults.length === 0) {
            toastr.info('No se encontraron resultados que coincidan con tu búsqueda');
        }
    }, 500);
}

function loadPromptImpl(context, id) {
    window.location.hash = `#chat-${id}`;
    context.loadFromHash();
    context.searchResults = [];
    context.searchQuery = '';
    toastr.success('Prompt cargado con éxito');
}

function formatDateImpl(dateString) {
    if (!dateString) return 'Fecha desconocida';
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

function togglePromptOptionsImpl(context) {
    context.showPromptOptions = !context.showPromptOptions;
}

function toggleFullscreenImpl(context) {
    const textarea = document.getElementById('generatedPrompt');

    if (!document.fullscreenElement) {
        if (textarea.requestFullscreen) {
            textarea.requestFullscreen();
        } else if (textarea.mozRequestFullScreen) {
            textarea.mozRequestFullScreen();
        } else if (textarea.webkitRequestFullscreen) {
            textarea.webkitRequestFullscreen();
        } else if (textarea.msRequestFullscreen) {
            textarea.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    context.showPromptOptions = false;
}

function toggleExecuteOptionsImpl(context) {
    context.showExecuteOptions = !context.showExecuteOptions;
}

function copyToClipboardImpl(context) {
    if (!context.generatedPrompt.trim()) {
        Swal.fire({
            title: 'Advertencia',
            text: 'No hay contenido para copiar',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Usar la API moderna del portapapeles si está disponible
    if (navigator.clipboard) {
        navigator.clipboard.writeText(context.generatedPrompt)
            .then(() => {
                toastr.success('El prompt ha sido copiado al portapapeles');
                context.showPromptOptions = false;
            })
            .catch(err => {
                console.error('Error al copiar: ', err);
                fallbackCopyToClipboardImpl(context);
            });
    } else {
        fallbackCopyToClipboardImpl(context);
    }
}

function fallbackCopyToClipboardImpl(context) {
    // Método de respaldo para navegadores que no soportan la API Clipboard
    const textarea = document.createElement('textarea');
    textarea.value = context.generatedPrompt;
    textarea.style.position = 'fixed';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        toastr.success('El prompt ha sido copiado al portapapeles');
    } catch (err) {
        console.error('Error al copiar: ', err);
        Swal.fire({
            title: 'Error',
            text: 'No se pudo copiar al portapapeles',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    document.body.removeChild(textarea);
    context.showPromptOptions = false;
}

function executeWithOptionImpl(context, option) {
    if (!context.generatedPrompt.trim()) {
        Swal.fire({
            title: 'Advertencia',
            text: 'Primero debes generar un prompt para ejecutarlo',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    context.executeWith = option;
    context.showExecuteOptions = false;

    // Mostrar spinner
    context.loading = true;

    // En una implementación real, aquí iría la lógica para enviar a ChatGPT o Claude
    console.log(`Ejecutando con ${option}:`, context.generatedPrompt);

    // Simular una petición Ajax
    setTimeout(() => {
        // Ocultar spinner
        context.loading = false;

        // Mostrar notificación de éxito
        toastr.success(`El prompt ha sido enviado a ${option}`);
    }, 1500);
}

function newFormImpl(context) {
    Swal.fire({
        title: '¿Crear nuevo prompt?',
        text: 'Se borrará el formulario actual',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, crear nuevo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            context.clearFormWithoutConfirmation();
            toastr.success('Nuevo formulario creado');
        }
    });
}

function prevPromptImpl(context) {
    // Get all saved prompts
    const savedPrompts = context.getSavedPrompts();
    if (savedPrompts.length === 0) {
        Swal.fire({
            title: 'Información',
            text: 'No hay prompts guardados para navegar',
            icon: 'info',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Mostrar loader
    context.loading = true;

    setTimeout(() => {
        // Find current index or default to last item
        let currentIndex = -1;
        const currentHash = window.location.hash;
        if (currentHash.startsWith('#chat-')) {
            const currentId = currentHash.split('-')[1];
            currentIndex = savedPrompts.findIndex(p => p.id === currentId);
        }

        if (currentIndex <= 0) {
            // Wrap around to the end if at beginning
            currentIndex = savedPrompts.length - 1;
        } else {
            currentIndex--;
        }

        // Navigate to the previous prompt
        const prevPrompt = savedPrompts[currentIndex];
        window.location.hash = `#chat-${prevPrompt.id}`;
        context.loadFromHash();

        // Ocultar loader
        context.loading = false;

        toastr.info(`Navegando: prompt ${currentIndex + 1} de ${savedPrompts.length}`);
    }, 400);
}

function nextPromptImpl(context) {
    // Get all saved prompts
    const savedPrompts = context.getSavedPrompts();
    if (savedPrompts.length === 0) {
        Swal.fire({
            title: 'Información',
            text: 'No hay prompts guardados para navegar',
            icon: 'info',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Mostrar loader
    context.loading = true;

    setTimeout(() => {
        // Find current index or default to -1 (before first item)
        let currentIndex = -1;
        const currentHash = window.location.hash;
        if (currentHash.startsWith('#chat-')) {
            const currentId = currentHash.split('-')[1];
            currentIndex = savedPrompts.findIndex(p => p.id === currentId);
        }

        if (currentIndex >= savedPrompts.length - 1 || currentIndex === -1) {
            // Wrap around to the beginning if at end
            currentIndex = 0;
        } else {
            currentIndex++;
        }

        // Navigate to the next prompt
        const nextPrompt = savedPrompts[currentIndex];
        window.location.hash = `#chat-${nextPrompt.id}`;
        context.loadFromHash();

        // Ocultar loader
        context.loading = false;

        toastr.info(`Navegando: prompt ${currentIndex + 1} de ${savedPrompts.length}`);
    }, 400);
}
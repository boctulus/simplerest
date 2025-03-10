function promptGenerator() {
    return {
        // Estado de la aplicación
        description: '',
        notes: '',
        generatedPrompt: '',
        filePaths: [],
        activeFileId: null,
        executeWith: '',
        searchQuery: '',
        searchResults: [],

        // Estado de navegación
        currentPage: 1,
        totalPages: 1,
        currentPromptIndex: -1,
        prompts: [],

        // Estado de UI
        showBulkActions: false,
        showExecuteOptions: false,
        showPromptOptions: false,
        loading: false,

        // Inicialización
        init() {
            this.addFilePath();
            this.loadFromHash();

            // Escuchar cambios en el hash de la URL
            window.addEventListener('hashchange', () => {
                this.loadFromHash();
            });

            // Auto-guardar cambios
            this.$watch('description', () => this.saveFormToLocalStorage());
            this.$watch('notes', () => this.saveFormToLocalStorage());
            this.$watch('filePaths', () => this.saveFormToLocalStorage(), {
                deep: true
            });

            // Asegurarse de que las librerías necesarias estén cargadas
            this.loadExternalLibraries();
        },

        // Cargar librerías externas necesarias (SweetAlert2 y Toastr)
        loadExternalLibraries() {
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
        },

        // Métodos para el buscador de prompts
        searchPrompts() {
            if (!this.searchQuery.trim()) {
                this.searchResults = [];
                return;
            }

            // Mostrar el loader
            this.loading = true;

            // Simular una búsqueda con delay
            setTimeout(() => {
                const query = this.searchQuery.toLowerCase();
                const allPrompts = this.getSavedPrompts();

                this.searchResults = allPrompts.filter(prompt => {
                    const description = (prompt.description || '').toLowerCase();
                    const notes = (prompt.notes || '').toLowerCase();
                    const paths = prompt.filePaths ? prompt.filePaths.map(f => f.path.toLowerCase()).join(' ') : '';

                    return description.includes(query) ||
                        notes.includes(query) ||
                        paths.includes(query);
                });

                // Ocultar el loader
                this.loading = false;

                if (this.searchResults.length === 0) {
                    toastr.info('No se encontraron resultados que coincidan con tu búsqueda');
                }
            }, 500);
        },

        loadPrompt(id) {
            window.location.hash = `#chat-${id}`;
            this.loadFromHash();
            this.searchResults = [];
            this.searchQuery = '';
            toastr.success('Prompt cargado con éxito');
        },

        formatDate(dateString) {
            if (!dateString) return 'Fecha desconocida';
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        },

        // Métodos para manipular rutas
        addFilePath() {
            const id = 'file-' + Date.now();
            this.filePaths.push({
                id: id,
                path: '',
                disabled: false,
                allowedFunctions: '',
                showFunctions: false,
                showDropdown: false,
                selected: false
            });
        },

        deleteFilePath(index) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres eliminar esta ruta?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.filePaths.splice(index, 1);
                    // Asegurar que siempre haya al menos una ruta
                    if (this.filePaths.length === 0) {
                        this.addFilePath();
                    }
                    toastr.success('La ruta ha sido eliminada');
                }
            });
        },

        toggleFileStatus(index) {
            this.filePaths[index].disabled = !this.filePaths[index].disabled;
            this.filePaths[index].showDropdown = false;
            toastr.info(this.filePaths[index].disabled ? 'Ruta deshabilitada' : 'Ruta habilitada');
        },

        toggleFunctions(index) {
            this.filePaths[index].showFunctions = !this.filePaths[index].showFunctions;
            this.filePaths[index].showDropdown = false;
        },

        toggleDropdown(id) {
            // Cerrar todos los demás dropdowns
            this.filePaths.forEach(file => {
                if (file.id !== id) file.showDropdown = false;
            });

            // Toggle el dropdown actual
            const file = this.filePaths.find(f => f.id === id);
            if (file) file.showDropdown = !file.showDropdown;
        },

        // Acciones masivas
        toggleBulkAction() {
            this.showBulkActions = !this.showBulkActions;
        },

        deleteSelectedPaths() {
            if (!this.hasSelectedPaths()) {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'No hay rutas seleccionadas para eliminar',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres eliminar las rutas seleccionadas?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.filePaths = this.filePaths.filter(file => !file.selected);
                    this.showBulkActions = false;

                    // Asegurar que siempre haya al menos una ruta
                    if (this.filePaths.length === 0) {
                        this.addFilePath();
                    }

                    toastr.success('Las rutas seleccionadas han sido eliminadas');
                }
            });
        },

        toggleSelectedPathsStatus(enable) {
            if (!this.hasSelectedPaths()) {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'No hay rutas seleccionadas',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            this.filePaths.forEach(file => {
                if (file.selected) {
                    file.disabled = !enable;
                }
            });
            this.showBulkActions = false;

            toastr.info(enable ? 'Rutas habilitadas' : 'Rutas deshabilitadas');
        },

        hasSelectedPaths() {
            return this.filePaths.some(file => file.selected);
        },

        // Manejo del prompt
        extractPathsFromPrompt() {
            const pathRegex = /(?:[a-zA-Z]:\\[^:\n]+)|(?:\/[^\n:]+)/g;
            const matches = this.description.match(pathRegex) || [];

            const validPaths = matches
                .map(path => path.trim())
                .filter(path => path && path.includes('.') && !path.endsWith('.'));

            if (validPaths.length === 0) {
                Swal.fire({
                    title: 'Información',
                    text: 'No se encontraron rutas de archivo en el texto del PROMPT',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Mostrar loader
            this.loading = true;

            // Simular proceso con setTimeout
            setTimeout(() => {
                // Agregar rutas que no existan ya
                let newPathsCount = 0;
                validPaths.forEach(path => {
                    const pathExists = this.filePaths.some(file => file.path === path);
                    if (!pathExists) {
                        this.filePaths.push({
                            id: 'file-' + Date.now() + Math.random().toString(36).substring(2),
                            path: path,
                            disabled: false,
                            allowedFunctions: '',
                            showFunctions: false,
                            showDropdown: false,
                            selected: false
                        });
                        newPathsCount++;
                    }
                });

                // Ocultar loader
                this.loading = false;

                toastr.success(`Se agregaron ${newPathsCount} ruta(s) desde el PROMPT`);
            }, 800);
        },

        clearForm() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres borrar todo el formulario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, borrar todo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.description = '';
                    this.notes = '';
                    this.generatedPrompt = '';
                    this.filePaths = [];
                    this.addFilePath();
                    localStorage.removeItem('currentForm');
                    history.pushState('', document.title, window.location.pathname);

                    toastr.success('El formulario ha sido borrado completamente');
                }
            });
        },

        copyToClipboard() {
            if (!this.generatedPrompt.trim()) {
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
                navigator.clipboard.writeText(this.generatedPrompt)
                    .then(() => {
                        toastr.success('El prompt ha sido copiado al portapapeles');
                        this.showPromptOptions = false;
                    })
                    .catch(err => {
                        console.error('Error al copiar: ', err);
                        this.fallbackCopyToClipboard();
                    });
            } else {
                this.fallbackCopyToClipboard();
            }
        },

        fallbackCopyToClipboard() {
            // Método de respaldo para navegadores que no soportan la API Clipboard
            const textarea = document.createElement('textarea');
            textarea.value = this.generatedPrompt;
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
            this.showPromptOptions = false;
        },

        // Métodos para navegación entre prompts guardados
        prevPrompt() {
            // Get all saved prompts
            const savedPrompts = this.getSavedPrompts();
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
            this.loading = true;

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
                this.loadFromHash();

                // Ocultar loader
                this.loading = false;

                toastr.info(`Navegando: prompt ${currentIndex + 1} de ${savedPrompts.length}`);
            }, 400);
        },

        nextPrompt() {
            // Get all saved prompts
            const savedPrompts = this.getSavedPrompts();
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
            this.loading = true;

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
                this.loadFromHash();

                // Ocultar loader
                this.loading = false;

                toastr.info(`Navegando: prompt ${currentIndex + 1} de ${savedPrompts.length}`);
            }, 400);
        },

        // Helper method to get all saved prompts sorted by timestamp
        getSavedPrompts() {
            const prompts = [];
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key.startsWith('chat-')) {
                    try {
                        const promptData = JSON.parse(localStorage.getItem(key));
                        if (promptData && promptData.id) {
                            prompts.push(promptData);
                        }
                    } catch (e) {
                        console.error('Error parsing saved prompt:', e);
                    }
                }
            }

            // Sort by timestamp (newest first)
            return prompts.sort((a, b) => {
                return new Date(b.timestamp || 0) - new Date(a.timestamp || 0);
            });
        },

        executeWithOption(option) {
            if (!this.generatedPrompt.trim()) {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'Primero debes generar un prompt para ejecutarlo',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            this.executeWith = option;
            this.showExecuteOptions = false;

            // Mostrar spinner
            this.loading = true;

            // En una implementación real, aquí iría la lógica para enviar a ChatGPT o Claude
            console.log(`Ejecutando con ${option}:`, this.generatedPrompt);

            // Simular una petición Ajax
            setTimeout(() => {
                // Ocultar spinner
                this.loading = false;

                // Mostrar notificación de éxito
                toastr.success(`El prompt ha sido enviado a ${option}`);
            }, 1500);
        },

        // Add method to save the current form as a new prompt
        saveAsNewPrompt() {
            // Mostrar spinner
            this.loading = true;

            setTimeout(() => {
                const formId = Date.now().toString(36) + Math.random().toString(36).substring(2);

                const formState = {
                    id: formId,
                    description: this.description,
                    notes: this.notes,
                    filePaths: this.filePaths.map(file => ({
                        path: file.path,
                        disabled: file.disabled,
                        allowedFunctions: file.allowedFunctions
                    })),
                    timestamp: new Date().toISOString()
                };

                localStorage.setItem(`chat-${formId}`, JSON.stringify(formState));
                window.location.hash = `#chat-${formId}`;

                // Ocultar spinner
                this.loading = false;

                toastr.success('El prompt ha sido guardado con un nuevo ID');
            }, 700);
        },

        generatePrompt() {
            // Verificar si hay rutas de archivos habilitadas
            const enabledPaths = this.filePaths.filter(file => !file.disabled && file.path.trim());

            if (enabledPaths.length === 0) {
                Swal.fire({
                    title: 'Advertencia',
                    text: 'No hay rutas de archivos habilitadas para incluir en el prompt',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Mostrar loader para simular la generación
            this.loading = true;

            // Simulamos la generación del prompt (en una implementación real, esto haría una petición al servidor)
            setTimeout(() => {
                let promptText = '';

                // Agregar introducción
                if (this.description.trim()) {
                    promptText += `${this.description}\n\n`;
                }

                // Agregar rutas de archivos habilitadas
                enabledPaths.forEach(file => {
                    const extension = file.path.split('.').pop().toLowerCase();
                    const language = {
                        'php': 'php',
                        'js': 'javascript',
                        'css': 'css',
                        'html': 'html',
                        'json': 'json'
                    } [extension] || '';

                    promptText += `/* Ruta: ${file.path} */\n\`\`\`${language}\n[Contenido del archivo ${file.path}]\n\`\`\`\n\n`;
                });

                // Agregar notas finales
                if (this.notes.trim()) {
                    promptText += `// Notas finales\n${this.notes}\n`;
                }

                this.generatedPrompt = promptText;

                // Ocultar loader
                this.loading = false;

                // Simular éxito o fallo de manera aleatoria (para demostración)
                // En tu código real, aquí verificarías el resultado real
                const success = true; // Harcodear éxito según lo solicitado

                if (success) {
                    // Mostrar notificación Toastr en caso de éxito
                    toastr.success('El prompt ha sido generado correctamente');
                } else {
                    // Mostrar SweetAlert en caso de error
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al generar el prompt. Por favor intenta nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }, 1000);
        },

        // Operaciones con el prompt generado
        togglePromptOptions() {
            this.showPromptOptions = !this.showPromptOptions;
        },

        toggleFullscreen() {
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

            this.showPromptOptions = false;
        },

        // Opciones de ejecución
        toggleExecuteOptions() {
            this.showExecuteOptions = !this.showExecuteOptions;
        },

        // Gestión del formulario
        newForm() {
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
                    this.clearFormWithoutConfirmation();
                    toastr.success('Nuevo formulario creado');
                }
            });
        },

        clearFormWithoutConfirmation() {
            this.description = '';
            this.notes = '';
            this.generatedPrompt = '';
            this.filePaths = [];
            this.addFilePath();
            localStorage.removeItem('currentForm');
            history.pushState('', document.title, window.location.pathname);
        },

        // Persistencia
        saveFormToLocalStorage() {
            const formState = {
                description: this.description,
                notes: this.notes,
                filePaths: this.filePaths.map(file => ({
                    path: file.path,
                    disabled: file.disabled,
                    allowedFunctions: file.allowedFunctions
                }))
            };

            localStorage.setItem('currentForm', JSON.stringify(formState));

            // Si hay un ID en el hash, también guardar con ese ID
            const currentHash = window.location.hash;
            if (currentHash.startsWith('#chat-')) {
                const formId = currentHash.split('-')[1];
                localStorage.setItem(`chat-${formId}`, JSON.stringify({
                    ...formState,
                    id: formId,
                    timestamp: new Date().toISOString()
                }));
            }
        },

        loadFromHash() {
            // Mostrar spinner al cargar
            this.loading = true;

            setTimeout(() => {
                const currentHash = window.location.hash;
                let savedForm;

                if (currentHash.startsWith('#chat-')) {
                    const formId = currentHash.split('-')[1];
                    savedForm = JSON.parse(localStorage.getItem(`chat-${formId}`));
                } else {
                    savedForm = JSON.parse(localStorage.getItem('currentForm'));
                }

                if (savedForm) {
                    this.description = savedForm.description || '';
                    this.notes = savedForm.notes || '';

                    // Cargar rutas de archivo
                    if (Array.isArray(savedForm.filePaths)) {
                        this.filePaths = savedForm.filePaths.map(file => ({
                            id: 'file-' + Date.now() + Math.random().toString(36).substring(2),
                            path: file.path || '',
                            disabled: file.disabled || false,
                            allowedFunctions: file.allowedFunctions || '',
                            showFunctions: false,
                            showDropdown: false,
                            selected: false
                        }));
                    }

                    // Asegurar que siempre haya al menos una ruta
                    if (this.filePaths.length === 0) {
                        this.addFilePath();
                    }
                }

                // Ocultar spinner después de cargar
                this.loading = false;
            }, 300);
        }
    };
}
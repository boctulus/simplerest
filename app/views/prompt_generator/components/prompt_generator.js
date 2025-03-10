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
            loadExternalLibraries();
        },

        // Métodos para el buscador de prompts
        searchPrompts() {
            return searchPromptsImpl(this);
        },

        loadPrompt(id) {
            return loadPromptImpl(this, id);
        },

        formatDate(dateString) {
            return formatDateImpl(dateString);
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

        // Method references to utility functions that have been moved to separate files
        prevPrompt() { return prevPromptImpl(this); },
        nextPrompt() { return nextPromptImpl(this); },
        getSavedPrompts() { return getSavedPromptsImpl(); },
        copyToClipboard() { return copyToClipboardImpl(this); },
        fallbackCopyToClipboard() { return fallbackCopyToClipboardImpl(this); },       
        executeWithOption(option) { return executeWithOptionImpl(this, option); },
        togglePromptOptions() { togglePromptOptionsImpl(this); },
        toggleFullscreen() { toggleFullscreenImpl(this); },
        toggleExecuteOptions() { return toggleExecuteOptionsImpl(this); },
        newForm() { return newFormImpl(this); },
        clearFormWithoutConfirmation() { return clearFormWithoutConfirmationImpl(this); },
        saveFormToLocalStorage() { return saveFormToLocalStorageImpl(this); },
        loadFromHash() { return loadFromHashImpl(this); },
        saveAsNewPrompt() { return saveAsNewPromptImpl(this); }
    };
}
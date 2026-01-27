// Toast configuration and utility functions

function configureToastr() {
    // Configurar Toastr si está disponible
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };
    }
}

// Auto-ejecutar la configuración cuando se cargue este script
(function() {
    // Verificar si toastr ya está cargado, si no, esperar a que se cargue
    if (typeof toastr !== 'undefined') {
        configureToastr();
    } else {
        // Esperar a que se cargue toastr
        document.addEventListener('DOMContentLoaded', function() {
            // Intentar configurar después de un breve retraso para asegurar que se haya cargado
            setTimeout(configureToastr, 500);
        });
    }
})();
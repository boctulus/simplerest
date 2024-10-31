<?php

/*
	This script is executed before anything else
*/

// Registrar la función para ejecutarse al terminar el script
// register_shutdown_function(function() {
//     // Obtener todos los archivos incluidos
//     $included_files = get_included_files();
    
//     // Ruta del archivo de log
//     $log_file = __DIR__ . '/../../../logs/includes.log';
    
//     // Formatear los datos para el log
//     $log_data = "Archivos incluidos en la solicitud:\n";
//     foreach ($included_files as $file) {
//         $log_data .= "$file\n";
//     }
//     $log_data .= "--------------------\n";
    
//     // Escribir en el archivo de log
//     file_put_contents($log_file, $log_data, FILE_APPEND);
// });

<style>    
    #info-switch:checked+label {
        background-color: #3b82f6;
    }

    /* Add touch-action for iOS compatibility */
    input[type="checkbox"],
    label,
    [role="button"] {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
</style>

<div class="relative">
    <!-- Tarjeta Principal -->
    <div
        class="relative flex flex-col md:flex-row items-center md:items-start p-6 bg-blue-900 text-white shadow-lg w-full max-w-4xl mx-auto">
        <!-- Imagen -->
        <img src="https://i.imgur.com/qwQQMGW.png" alt="Foto de perfil"
            class="w-32 h-32 rounded-full object-cover md:mr-6">

        <!-- Contenido de texto -->
        <div class="flex-1 mt-4 md:mt-0">
            <h2 class="text-2xl font-bold">Guillermo Olvera</h2>
            <p class="text-sm text-gray-400">REDES</p>
            <span class="inline-block bg-yellow-500 text-white text-sm font-semibold px-2 py-1 rounded mt-2">
                ⭐ 5.0 Reseñas
            </span>
            <p class="mt-4">
                Soy un desarrollador web con una amplia gama de conocimientos en <strong>muchos lenguajes front-end y
                    back-end</strong>, marcos responsivos, bases de datos, y mejores prácticas de código.
            </p>
            <p class="mt-2 text-sm text-gray-400">
                📧 guillermo.olvera@tvc.mx 🌍 México
            </p>
        </div>

        <!-- Botones (círculares) -->
        <div class="absolute top-4 right-4 flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-2">
            <div class="w-8 h-8 bg-gray-500 rounded-full cursor-pointer hover:bg-gray-600 transition-colors flex items-center justify-center"
                onclick="alert('Editar perfil')" role="button">
                <i class="fas fa-pencil-alt text-white"></i>
            </div>
            <div class="w-8 h-8 bg-gray-500 rounded-full cursor-pointer hover:bg-gray-600 transition-colors flex items-center justify-center"
                onclick="alert('Descargar como PDF')" role="button">
                <i class="fas fa-file-pdf text-white"></i>
            </div>
            <div class="w-8 h-8 bg-red-500 rounded-full cursor-pointer hover:bg-red-600 transition-colors flex items-center justify-center"
                onclick="alert('Borrar perfil')" role="button">
                <i class="fas fa-trash text-white"></i>
            </div>
        </div>

    </div>

    <!-- Switch y contenido adicional -->
    <div class="relative max-w-4xl mx-auto">
        <!-- Switch -->
        <input type="checkbox" id="info-switch" class="hidden peer">
        <label for="info-switch" class="absolute -top-12 right-4 w-12 h-6 bg-gray-600 rounded-full peer-checked:bg-blue-500 cursor-pointer transition-all duration-300 
                        before:content-[''] before:absolute before:top-1 before:left-1 before:w-4 before:h-4 before:bg-white before:rounded-full 
                        before:transition-all before:duration-300 peer-checked:before:translate-x-6">
        </label>

        <!-- Contenido adicional -->
        <div class="mt-2 text-white shadow-lg overflow-hidden transition-all duration-300 max-h-0 peer-checked:max-h-[500px]"
            style="background-color: #e7e7e7">
            <div class="p-4">

                <div class="p-4 bg-gray-50 rounded-md shadow-md max-w-full md:max-w-4xl mx-auto">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                        <!-- Nombre y puesto -->
                        <div class="mb-4 md:mb-0">
                            <h1 class="text-lg font-semibold text-gray-800">Juan Guillermo Olvera Maldonado</h1>
                            <p class="text-gray-600">Coordinador de Ingeniería</p>
                        </div>

                        <div class="mb-4 text-sm text-gray-600 text-right md:text-left">
                            <p>MTY</p>
                        </div>

                        <!-- Contacto -->
                        <div class="text-sm text-gray-600 text-right md:text-left">
                            <p>guillermo.olvera@tvc.mx</p>
                            <p>81-8400-1777 #27363</p>
                        </div>
                    </div>

                    <!-- Contenido dividido en columnas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm text-gray-700">
    <!-- Columna 1 -->
    <div>
        <p class="mb-2"><strong>Marcas:</strong></p>
        <ul>
            <li>Ubiquiti</li>
            <li>Draytek</li>
            <li>Utepo</li>
            <li>Tp-Link</li>
        </ul>
    </div>
    <!-- Columna 2 -->
    <div>
        <p class="mb-2"><strong>Certificaciones:</strong></p>
        <ul>
            <li>Ubiquiti: UEWA</li>
            <li>Draytek: DISCOVERY</li>
            <li>Tp-Link: OMA</li>
        </ul>
    </div>
    <!-- Columna 3 - Mantener igual -->
    <div>
        <p class="mb-2"><strong>Habilidades:</strong></p>
        <ul>
            <li>Ubiquiti UniFi: <span class="text-yellow-500">★★★★★</span> (5)</li>
            <li>Draytek: <span class="text-yellow-500">★★★★</span> (4)</li>
            <li>Vivotek: <span class="text-yellow-500">★★★</span> (3)</li>
            <li>Tp-Link Omada: <span class="text-yellow-500">★★★</span> (3)</li>
        </ul>
    </div>
</div>


                </div>


            </div>
        </div>


    </div>
</div>
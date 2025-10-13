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
    class="relative flex flex-col md:flex-row items-center md:items-start p-6 text-gray-400 shadow-lg w-full md:w-1/2 lg:w-1/3 mx-auto">
        <!-- Imagen -->       
        <img src="https://i.imgur.com/qwQQMGW.png" alt="Foto de perfil"
            class="w-40 h-40 rounded-full object-cover md:mr-6" style="padding-top: 5px;">

        <!-- Contenido de texto -->
        <div class="flex-1 mt-4 md:mt-0">
            <h2 class="text-2xl font-bold text-gray-900">Guillermo Olvera</h2>
            <p class="text-sm text-blue-900 font-semibold"><a href="/personal/redes">Redes</a></p>
            <p class="text-sm text-gray-400 mt-1">11 Cursos</p>
            <span class="inline-block bg-yellow-500 text-white text-sm font-semibold px-2 py-1 rounded mt-2">
                ⭐ 5.0 Calificaciones
            </span>

            <!-- Social Media Icons -->
            <div class="flex space-x-2 mt-4">
                <a href="#" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fab fa-facebook-f text-gray-500 hover:text-blue-900"></i>
                </a>
                <a href="#" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fab fa-twitter text-gray-500 hover:text-blue-900"></i>
                </a>
                <a href="#" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fab fa-linkedin-in text-gray-500 hover:text-blue-900"></i>
                </a>
                <a href="#" class="w-8 h-8 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fab fa-github text-gray-500 hover:text-blue-900"></i>
                </a>

            </div>
            
        </div>

    </div>
</div>

    
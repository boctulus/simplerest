<script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            clifford: '#da373d',
          }
        }
      }
    }
</script>

<style>
    body {
        background-color: #002D62;
    }
</style>

<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden md:flex">
    <!-- Imagen -->
    <div class="md:w-1/3">
        <img src="/app/views/instructors/programmer.png" alt="Foto de Guillermo Olvera" class="w-full h-full object-cover">
    </div>
    <!-- Contenido -->
    <div class="flex flex-col justify-between p-6 md:w-2/3">
        <!-- Cabecera -->
        <div>
            <h2 class="text-blue-900 text-2xl font-extrabold">Guillermo Olvera</h2>
            <p class="text-blue-600 text-sm uppercase tracking-wide font-medium">Redes</p>
            <div class="mt-2">
                <span class="inline-block bg-yellow-200 text-yellow-800 text-xs font-bold px-4 py-1 rounded-full">
                    ⭐ 5.0 Reseñas
                </span>
            </div>
        </div>
        <!-- Descripción -->
        <p class="text-gray-700 mt-4 leading-relaxed font-light">
            Soy un desarrollador web con una amplia gama de conocimientos en <span class="font-semibold text-gray-900">muchos lenguajes front-end y back-end</span>, marcos responsivos, bases de datos, y mejores prácticas de código.
        </p>
        <!-- Información adicional -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 text-sm">
                <a href="mailto:guillermo.olvera@tvc.mx" class="text-blue-500 hover:underline font-medium">
                    📧 guillermo.olvera@tvc.mx
                </a>
                <p class="text-gray-500">📍 México</p>
            </div>
            <div class="mt-4 sm:mt-0 flex justify-center space-x-4">
                <a href="#" class="text-blue-500 hover:text-blue-700 text-lg">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="#" class="text-blue-500 hover:text-blue-700 text-lg">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="text-blue-500 hover:text-blue-700 text-lg">
                    <i class="fas fa-link"></i>
                </a>
            </div>
        </div>
    </div>
</div>

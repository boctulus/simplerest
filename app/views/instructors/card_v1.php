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

<div class="max-w-sm mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="bg-blue-900 p-6 text-center">
        <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden">
            <img src="/app/views/instructors/programmer.png" alt="Foto de Guillermo Olvera">
        </div>
        <h2 class="text-white text-2xl font-extrabold">Guillermo Olvera</h2>
        <p class="text-blue-200 text-sm uppercase tracking-wide font-medium">Redes</p>
        <div class="mt-2">
            <span class="inline-block bg-yellow-200 text-yellow-800 text-xs font-bold px-4 py-1 rounded-full">
                ⭐ 5.0 Reseñas
            </span>
        </div>
    </div>
    <div class="p-6">
        <p class="text-gray-700 mb-4 leading-relaxed font-light">
            Soy un desarrollador web con una amplia gama de conocimientos en <span class="font-semibold text-gray-900">muchos lenguajes front-end y back-end</span>, marcos responsivos, bases de datos, y mejores prácticas de código.
        </p>
        <div class="flex items-center space-x-4 text-sm">
            <a href="mailto:guillermo.olvera@tvc.mx" class="text-blue-500 hover:underline font-medium">
                📧 guillermo.olvera@tvc.mx
            </a>
            <p class="text-gray-500">📍 México</p>
        </div>
        <div class="mt-4 flex justify-center space-x-4">
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

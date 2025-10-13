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

<div class="relative flex flex-col md:flex-row items-center md:items-start p-6 bg-blue-900 text-white rounded-lg shadow-lg w-full max-w-4xl mx-auto">
  <!-- Imagen -->
  <img src="/app/views/instructors/programmer.png" alt="Foto de perfil" class="w-32 h-32 rounded-full object-cover md:mr-6">

  <!-- Contenido de texto -->
  <div class="flex-1 mt-4 md:mt-0">
    <h2 class="text-2xl font-bold">Guillermo Olvera</h2>
    <p class="text-sm text-gray-400">REDES</p>
    <span class="inline-block bg-yellow-500 text-black text-sm font-semibold px-2 py-1 rounded mt-2">
      ⭐ 5.0 Reseñas
    </span>
    <p class="mt-4">
      Soy un desarrollador web con una amplia gama de conocimientos en <strong>muchos lenguajes front-end y back-end</strong>, marcos responsivos, bases de datos, y mejores prácticas de código.
    </p>
    <p class="mt-2 text-sm text-gray-400">
      📧 guillermo.olvera@tvc.mx 🌍 México
    </p>
  </div>

  <!-- Botones (círculos grises) -->
  <div class="absolute top-4 right-4 flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-2">
    <div class="w-8 h-8 bg-gray-500 rounded-full"></div>
    <div class="w-8 h-8 bg-gray-500 rounded-full"></div>
    <div class="w-8 h-8 bg-gray-500 rounded-full"></div>
  </div>
</div>

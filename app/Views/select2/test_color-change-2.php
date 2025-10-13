<style>
  /*
    Ensayado con SELECT 4.0.x
  */

  select {
    width: 300px;
  }

  /* Placeholder */
  .select2-selection__placeholder {
    color: red !important;
  }

  /* Cambia colores de las options excepto de la que tiene foco y de la selccionada */
  .select2-results, .select2-selection__rendered { 
    color: red;
  }

  /* Option que esta recibiendo el foco al navegar entre opciones */
  .select2-results__option--highlighted { 
    color: yellow !important;
  }

  /* Option selected */
  .select2-selection__rendered { 
    color: blue !important;
  }

  /* Border */
  .select2-container {
    border: 5px solid red;
  }   
</style>

<h1 style="margin-bottom: 20px;">SELECT2 cambio de colores</h1>

<select class="select2" name="fruit">
    <option></option>
    <option class="red-option">Apple</option>
    <option class="green-option">Kiwi</option> 
    <option class="blue-option">Grape</option> 
</select>

<script>
$(document).ready(function() {
  $(".select2").select2({
    placeholder:"Seleccione ...",
    'allowClear': true
  });
});
</script>
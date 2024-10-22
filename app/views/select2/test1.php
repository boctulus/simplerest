<style>
  select {
    width: 300px;
  }

  /* Cambia colores de las options */
  .select2-container--default {
    color: red !important;
  }

  /* Cambia colores de las options de items "disabled" */
  .select2-container--default .select2-results__option[aria-disabled=true] {
    color: #999;
  }

  /* Tambien cambia colores de las options */
  .select2-results { 
    color: yellow;
  }

  /* Cambia colores de las options espcificos */
  .select2-results__option:nth-child(1) {
    color: red;
  }
  .select2-results__option:nth-child(2) {
    color: green;
  }
  .select2-results__option:nth-child(3) {
    color: blue;
  }

  /* Input field */
.select2-selection__rendered {  }
    
  /* Around the search field */
  .select2-search {  }
      
  /* Search field */
  .select2-search input {  }
      
  /* Each result */
  .select2-results {  }
      
  /* Higlighted (hover) result */
  .select2-results__option--highlighted {  }
      
  /* Selected option */
  .select2-results__option[aria-selected=true] {  }

  /* Placeholder */
  .select2-selection__placeholder {
      color: red !important;
  }
</style>

<h1 style="margin-bottom: 20px;">SELECT2 ejemplo minimo</h1>

<select class="select2 select2-countries" id="countries" style="width:300px">
  <option value="">País</option>
</select>

<script>
  $(document).ready(function() {
    // Inicializa Select2 en el select
    $('#countries').select2({
      placeholder: "Selecciona un país",
      allowClear: true,
      data: [
        { id: 'us', text: 'United States' },
        { id: 'mx', text: 'Mexico' },
        { id: 'fr', text: 'France' }
      ]
    });
  });

  // Suscripción al evento change
  $('#countries').on('change', function() {
      var selectedValue = $(this).val(); // Valor seleccionado
      var selectedText = $(this).find('option:selected').text(); // Texto del valor seleccionado
      console.log("El valor seleccionado es:", selectedValue);
      console.log("El texto del país seleccionado es:", selectedText);
    });
</script>
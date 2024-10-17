<h1 style="margin-bottom: 20px;">SELECT2 ejemplo minimo</h1>

<select class="select2-countries" id="countries" style="width:300px">
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
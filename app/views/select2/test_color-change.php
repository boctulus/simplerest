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
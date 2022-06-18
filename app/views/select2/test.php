<script>

  // General
  function setDropdownOptions(select_elem, options, default_option){
    select_elem.innerHTML = '';

    let opt = new Option(default_option['text'], default_option['value']);
    opt.setAttribute('selected', true);
    select_elem.appendChild(opt);

    if (typeof options == 'undefined' || options == null || options.length == 0){
      select_elem.disabled = true;
      return;
    } else {
      select_elem.disabled = false;
    }

    for (let i=0; i<options.length; i++){
      let opt = new Option(options[i]['text'], options[i]['value']);
      select_elem.appendChild(opt);
    }
  }

  let countries_elem;
  
  let country_items = [];
  let state_items = [];


  $(document).ready(function() {
      $('.js-example-basic-multiple').select2();

      countries_elem = document.getElementById('countries');

      fill_countries()

      setDropdownOptions(countries_elem, country_items, {'text': 'Pais', 'value': ''});
  });

  const arr = <?= $json ?>

  const array_column = (array, column) => {
      return array.map(item => item[column]);
  };

  const countries    = array_column(arr['countries'], 'country');

  function fill_countries(){      
      for (var i=0; i<countries.length; i++)
      {
        country_items.push({
          'text': countries[i],
          'value': countries[i]
        });
    }
  }




</script>

<h3>Test Select2</h3>


<select class="js-example-basic-multiple" name="countries[]" id="countries" style="width:300px">
  
</select>

<!-- <select class="js-example-basic-multiple" name="states[]" style="width:300px">
  <option value="AL">Alabama</option>
    ...
  <option value="TX">Texas</option>
  <option value="TN">Teneesee</option>
  
  <option value="WY">Wyoming</option>
</select> -->


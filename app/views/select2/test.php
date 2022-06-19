<script>

  function setDropdownOptionsSelect2(select_elem, options, default_option){
    // clear
    jQuery(select_elem).val(null).trigger('change');
    jQuery(select_elem).val(null).empty().select2('destroy')
    jQuery(select_elem).val('').trigger('change')
    jQuery(select_elem).select2({data: {id:null, text: null}})

    jQuery(select_elem).select2({data: options});
  }

  let countries_elem;
  let states_elem;
  
  let country_items = [];
  let state_items = [];

  /*
    Genera valores para rellenar el select
  */
  function fill_countries(){      
    for (var i=0; i<countries.length; i++)
    {
      country_items.push({
        'id': countries[i],
        'text': countries[i]
      });
    }
  }

  function fill_states(states){      
    for (var i=0; i<states.length; i++)
    {
      state_items.push({
        'id': states[i],
        'text': states[i]
      });
    }
  }


  $(document).ready(function() {
      $('.select2-countries').select2();
      $('.select2-states').select2();

      countries_elem = document.getElementById('countries');
      states_elem    = document.getElementById('states');

      fill_countries()
      setDropdownOptionsSelect2(countries_elem, country_items, {'text': 'Pais', 'value': ''});

      jQuery(countries_elem).change(function(){
        let country_name_selected = countries_elem.value;
        
        let state_name_selected = find_country(country_name_selected);

        if (state_name_selected != null){
          state_items = [];
          fill_states(state_name_selected)
          setDropdownOptionsSelect2(states_elem, state_items, {'text': 'Provincia o estado', 'value': ''});
        }

      });
  });

  const arr = <?= $json ?>

  const array_column = (array, column) => {
      return array.map(item => item[column]);
  };

  const countries = array_column(arr['countries'], 'country');

  /*
    Busca por pais y devuelve los estados
  */
  function find_country(name){
    for (var i=0; i<arr['countries'].length; i++)
    {
      if (arr['countries'][i]['country'] == name){
          return arr['countries'][i]['states'];
      }
    }

    return null;
  }


</script>

<h3>Test Select2</h3>

<p></p>

<select class="select2-countries" name="countries[]" id="countries" style="width:300px">
</select>

<p></p>

<select class="select2-states" name="states[]" id="states" style="width:300px">
  <option value="">Provincia / estado</option>
</select>


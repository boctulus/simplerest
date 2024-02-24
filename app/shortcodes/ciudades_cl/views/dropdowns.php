<script>
  const arr = <?= $json ?>

  document.addEventListener("DOMContentLoaded", (event) => {
    if (typeof $ == 'undefined') {
      $ = jQuery;
    }

    $('.select2-states').select2();
    $('.select2-cities').select2();

    states_elem = document.getElementById('states');
    cities_elem = document.getElementById('cities');

    fill_states();
    setSelect2Options(states_elem, state_items, {'id': 'NULL', 'text': 'Región'});

    $(states_elem).change(function(){
      let state_name_selected = states_elem.value;

      let cities = find_state_cities(state_name_selected);

      if (cities != null){
        city_items = [];
        fill_cities(cities);
        setSelect2Options(cities_elem, city_items, {'id': 'NULL', 'text': 'Ciudad'});
      }

    });
  });

  function setSelect2Options(select_elem, options, default_option){
    // clear
    $(select_elem).val(null).trigger('change')
    $(select_elem).val(null).empty().select2('destroy')
    $(select_elem).val('').trigger('change')
    $(select_elem).select2({data: {id:null, text: null}})

    // agrego delante la opción por defecto
    options.unshift(default_option)

    $(select_elem).select2({data: options});
  }

  let states_elem;
  let cities_elem;

  let state_items = [];
  let city_items = [];

  /*
    Genera valores para rellenar el select de estados
  */
  function fill_states(){
    for (var i=0; i<arr['states'].length; i++)
    {
      state_items.push({
        'id': arr['states'][i]['state'],
        'text': arr['states'][i]['state']
      });
    }
  }

  function fill_cities(cities){
    for (var i=0; i<cities.length; i++)
    {
      city_items.push({
        'id': cities[i],
        'text': cities[i]
      });
    }
  }

  /*
    Busca por estado y devuelve las ciudades
  */
  function find_state_cities(name){
    for (var i=0; i<arr['states'].length; i++)
    {
      if (arr['states'][i]['state'] == name){
        return arr['states'][i]['cities'];
      }
    }

    return null;
  }
</script>

<h3>Dropdowns dependientes</h3>

<p></p>

<select class="select2-states" name="states[]" id="states" style="width:300px">
  <option value="">Estado</option>
</select>

<p></p>

<select class="select2-cities" name="cities[]" id="cities" style="width:300px">
  <option value="">Ciudad</option>
</select>

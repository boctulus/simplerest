<script>
  const arr = <?= $json ?>

  document.addEventListener("DOMContentLoaded", (event) => {
    if (typeof $ == 'undefined') {
      $ = jQuery;
    }

    $('.select2-countries').select2();
    $('.select2-states').select2();

    countries_elem = document.getElementById('countries');
    states_elem = document.getElementById('states');

    fill_countries()
    setSelect2Options(countries_elem, country_items, { 'id': 'NULL', 'text': 'Pais' });

    $(countries_elem).change(function () {
      let country_name_selected = countries_elem.value;

      let state_name_selected = find_country(country_name_selected);

      if (state_name_selected != null) {
        state_items = [];
        fill_states(state_name_selected)
        setSelect2Options(states_elem, state_items, { 'id': 'NULL', 'text': 'Provincia o estado' });
      }

    });
  });

  function setSelect2Options(select_elem, options, default_option) {
    // clear
    $(select_elem).val(null).trigger('change')
    $(select_elem).val(null).empty().select2('destroy')
    $(select_elem).val('').trigger('change')
    $(select_elem).select2({ data: { id: null, text: null } })

    // agrego delante la opcion por defecto
    options.unshift(default_option)

    $(select_elem).select2({ data: options });
  }

  let countries_elem;
  let states_elem;

  let country_items = [];
  let state_items = [];

  /*
    Genera valores para rellenar el select
  */
  function fill_countries() {
    for (var i = 0; i < countries.length; i++) {
      country_items.push({
        'id': countries[i],
        'text': countries[i]
      });
    }
  }

  function fill_states(states) {
    for (var i = 0; i < states.length; i++) {
      state_items.push({
        'id': states[i],
        'text': states[i]
      });
    }
  }

  const array_column = (array, column) => {
    return array.map(item => item[column]);
  };

  const countries = array_column(arr['countries'], 'country');

  /*
    Busca por pais y devuelve los estados
  */
  function find_country(name) {
    for (var i = 0; i < arr['countries'].length; i++) {
      if (arr['countries'][i]['country'] == name) {
        return arr['countries'][i]['states'];
      }
    }

    return null;
  }


</script>

<h3>Dropdowns dependientes</h3>

<p></p>

<select class="select2-countries" name="countries[]" id="countries" style="width:300px">
  <option value="">País</option>
</select>

<p></p>

<select class="select2-states" name="states[]" id="states" style="width:300px">
  <option value="">Provincia / estado</option>
</select>
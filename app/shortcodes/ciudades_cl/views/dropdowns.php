<style>
    #botonera-cambio-direccion {
        font-family: Arial, sans-serif;
    }

    .btn-pill {
        display: block;
        width: 100%;
        /* Cambié el ancho para que ocupen toda la línea */
        padding: 10px;
        margin: 10px 0;
        color: #fff;
        border: none;
        border-radius: 25px;
        cursor: pointer;
    }

    .btn-info {
        background-color: #007bff;
    }
</style>

<div class="container" id="botonera-cambio-direccion">
    <h3>Cambiar mi ubicación</h3>

    <div class="row mt-5">
        <div class="col-md-12">
            <select class="select2-states" name="states[]" id="states" style="width:100%">
                <option value="">Estado</option>
            </select>
        </div>

        <div class="col-md-12 mt-3">
            <select class="select2-cities" name="cities[]" id="cities" style="width:100%">
                <option value="">Ciudad</option>
            </select>
        </div>

        <div class="col-12 mt-4 d-flex" role="group">
            <button type="button" class="btn btn-info btn-pill me-2" data-bs-dismiss="modal"
                id="btnMantener">Mantener</button>
            <button type="button" class="btn btn-success btn-pill" id="btnCambiar">Cambiar</button>
        </div>
    </div>
</div>


<script>
    const arr           = <?= $json ?>;
    const regionInicial = "Antofagasta";  // debe provenir del backend
    const ciudadInicial = "Calama";       // debe provenir del backend

    // Función para seleccionar un valor inicial en un selector
    const selectInitialValue = (select_elem, value) => {
        $(select_elem).val(value).trigger('change');
    };

    document.addEventListener("DOMContentLoaded", (event) => {
        if (typeof $ == 'undefined') {
            $ = jQuery;
        }

        $('.select2-states').select2();
        $('.select2-cities').select2();

        states_elem = document.getElementById('states');
        cities_elem = document.getElementById('cities');

        fill_states();
        setSelect2Options(states_elem, state_items, { 'id': 'NULL', 'text': 'Región' });
        selectInitialValue(states_elem, regionInicial); //

        // Evento de cambio en el selector de regiones
        $(states_elem).change(function () {
            let state_name_selected = states_elem.value;

            let cities = find_state_cities(state_name_selected);

            if (cities != null) {
                city_items = [];
                fill_cities(cities);
                setSelect2Options(cities_elem, city_items, { 'id': 'NULL', 'text': 'Ciudad' });
            }
        });

        // Llenar y seleccionar la ciudad inicial
        if (regionInicial) {
            selectInitialValue(states_elem, regionInicial);

            if (ciudadInicial) {
                selectInitialValue(cities_elem, ciudadInicial);
            }
        }

        // Agrega el evento de clic al botón "Mantener"
        document.getElementById('btnMantener').addEventListener('click', function () {
            // Cierra el modal (ajusta esta línea según cómo cierres el modal en tu código)
            closeAddrModal();   
        });

        // Agrega el evento de clic al botón "Cambiar"
        document.getElementById('btnCambiar').addEventListener('click', function () {
            // Obtiene y muestra el value de cada SELECT en la consola
            console.log('Estado seleccionado:', states_elem.value);
            console.log('Ciudad seleccionada:', cities_elem.value);
        });
    });

    function setSelect2Options(select_elem, options, default_option) {
        // clear
        $(select_elem).val(null).trigger('change')
        $(select_elem).val(null).empty().select2('destroy')
        $(select_elem).val('').trigger('change')
        $(select_elem).select2({ data: { id: null, text: null } })

        // agrego delante la opción por defecto
        options.unshift(default_option)

        $(select_elem).select2({ data: options });
    }

    let states_elem;
    let cities_elem;

    let state_items = [];
    let city_items = [];

    /*
      Genera valores para rellenar el select de estados
    */
    function fill_states() {
        for (var i = 0; i < arr['states'].length; i++) {
            state_items.push({
                'id': arr['states'][i]['state'],
                'text': arr['states'][i]['state']
            });
        }
    }

    function fill_cities(cities) {
        for (var i = 0; i < cities.length; i++) {
            city_items.push({
                'id': cities[i],
                'text': cities[i]
            });
        }
    }

    /*
      Busca por estado y devuelve las ciudades
    */
    function find_state_cities(name) {
        for (var i = 0; i < arr['states'].length; i++) {
            if (arr['states'][i]['state'] == name) {
                return arr['states'][i]['cities'];
            }
        }

        return null;
    }
</script>
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

    #states, #cities {
        z-index: 999999;
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
    const arr = <?= $json ?>;
    let regionInicial;
    let ciudadInicial;
    let states_elem;
    let cities_elem;
    let state_items = [];
    let city_items  = [];

    // Función para habilitar o deshabilitar el botón "Cambiar" según los valores seleccionados
    const toggleChangeButton = () => {
        const stateValue = states_elem.value;
        const cityValue = cities_elem.value;

        if (stateValue && cityValue) {
            document.getElementById('btnCambiar').disabled = false;
        } else {
            document.getElementById('btnCambiar').disabled = true;
        }
    };

    // Función para establecer los valores predeterminados de estado y ciudad
    const setDefaultValues = (state, city) => {
        selectInitialValue(states_elem, state);
        selectInitialValue(cities_elem, city);
    };

    // Realizar la solicitud inicial al servidor
    let initialSettings = {
        "url": "http://woo5.lan/address/get?__user_id=1",  // <-- arreglar hardcodeo y quitar ?__user_id
        "method": "GET",
        "headers": {
            "Accept": "application/json"
        }
    };

    // Función para seleccionar un valor inicial en un selector
    const selectInitialValue = (select_elem, value) => {
        $(select_elem).val(value).trigger('change');
    };

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

        $.ajax(initialSettings)
        .done(function(response) {
            const { state, city } = response.data;
            setDefaultValues(state, city);
            toggleChangeButton(); // Verificar si se habilita el botón "Cambiar"
        }); 

        // Evento de cambio en el selector de regiones
        $(states_elem).change(function () {
            let state_name_selected = states_elem.value;

            let cities = find_state_cities(state_name_selected);

            if (cities != null) {
                city_items = [];
                fill_cities(cities);
                setSelect2Options(cities_elem, city_items, { 'id': 'NULL', 'text': 'Ciudad' });
                toggleChangeButton(); // Verificar si se habilita el botón "Cambiar"
            }
        });

        // Llenar y seleccionar la ciudad inicial
        if (regionInicial) {
            selectInitialValue(states_elem, regionInicial);

            if (ciudadInicial) {
                selectInitialValue(cities_elem, ciudadInicial);
            }
        }

        // Función para enviar el request al presionar el botón "Cambiar"
        function sendChangeRequest(state, city) {
            var changeSettings = {
                "url": "http://woo5.lan/address/change?__user_id=1",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify({
                    "state": state,
                    "city": city
                }),
            };

            $.ajax(changeSettings)
            .done(function (response) {
                console.log(response);
                // Aquí puedes realizar cualquier otra acción después de cambiar la dirección
            });
        }      

        // Agrega el evento de clic al botón "Mantener"
        document.getElementById('btnMantener').addEventListener('click', function () {
            // Cierra el modal (ajusta esta línea según cómo cierres el modal en tu código)
            closeAddrModal();   
        });

        // Agrega el evento de clic al botón "Cambiar"
        document.getElementById('btnCambiar').addEventListener('click', function () {
            // 4. Enviar un request al presionar el botón "Cambiar" con los valores del formulario.
            let selectedState = states_elem.value;
            let selectedCity = cities_elem.value;

            console.log('Estado seleccionado:', selectedState);
            console.log('Ciudad seleccionada:', selectedCity);

            if (selectedState && selectedCity) {
                sendChangeRequest(selectedState, selectedCity);
            }
        });

        // Evento de cambio en el selector de ciudades
        $(cities_elem).change(function() {
            toggleChangeButton(); // Verificar si se habilita el botón "Cambiar"
        });

    });

   
</script>
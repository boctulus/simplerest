<?php

use Google\Service\CloudNaturalLanguage\Document;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
    use simplerest\core\libs\HtmlBuilder\Tag;

    Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
?>

<h3>Prueba con Ajax con paginacion</h3>

<?php

    $entity   = "products";
    $tenantid = "az";

    js_file('vendors/axios/axios.min.js', null, true);
    //js_file('vendors/lodash/lodash.min.js', null, true);
    js_file('js/bt-utilities.js');
    js_file('js/utilities.js');

    css_file('css/bt-custom.css');

    echo tag('buttonGroup')->content([
		tag('button')->content('Nuevo')
        ->class('no-border')
        ->style('margin-right:5px;')
        ->id('btn-create')
        ->info(),
		
        tag('button')->content('Borrar')
        ->class('no-border')
        ->id('btn-multiple-delete')
        ->danger()        
	])
    ->class('my-3');

    /*
        Dado que no estoy usando un framework reactivo,
        las definiciones pueden directamente ofrecerse en el backend
        evitandome otro request.
    */

    $defs = get_defs($entity, $tenantid, false, false);

    js("
        const entity   = '" . $entity . "';
        const tenantid = '" . $tenantid . "';

        let   defs     = " . json_encode($defs) . ";
    ", null, true);

?>

<script>
    let checked = [];

    window.addEventListener('DOMContentLoaded', (event) => {
        document.getElementById('btn-create').onclick = function () { 
            $('#row-form-modal').show()
        };

        document.getElementById('btn-multiple-delete').onclick = function () { 
            let checked_count = checked.length; 

            if (!confirm(`Está por borrar ${checked_count} registros. Está seguro?`)) {
                return;
            }

            const col_res = create_collection(entity, checked);
            col_res.then((res) => {
                console.log('id', res.data.id)
            })
        };
    });    
    
</script>

<div id="example-table"></div>

<?php
    /*
        Crear / editar row 
    */

    echo tag('modal')->content(
        tag('modalDialog')->content(
            tag('modalContent')->content(
                tag('modalHeader')->content(
                    tag('modalTitle')->text('Nuevo') . 
                    tag('closeButton')->dataBsDismiss('modal')
                ) .
                tag('modalBody')->content(
                    tag('p')->text('Aca irian los campos')
                ) . 
                tag('modalFooter')->content(
                    tag('closeModal')->content("Cancelar")->attributes([ 'onClick' => "hide_elem_by_id('row-form-modal');" ]) .
                    tag('button')->text('Guardar')
                ) 
            ) 
        )
    )->id('row-form-modal');

?>

<!--
    Para agregar botones al header ver (sin probar)

    https://stackoverflow.com/questions/67695811/tabulator-add-a-button-in-a-column-header
-->

<script>
    const token   = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url = `${base_url}/api/v1/${entity}`;

    let table   = {};
    let columns = [];
    let res     = {};

    function checkboxSelected(id){
        elem = document.getElementById(id);

        if (elem.checked){
            checked.push(id)
        } else {
            var index = checked.indexOf(id);
            if (index !== -1) {
                checked.splice(index, 1);
            }
        }

        //console.log (checked)
    }

    async function create_collection(entity, id_ay) {
        const url  = `${base_url}/api/v1/collections`;

        const data = {
            entity:entity,
            refs:id_ay 
        }

        let body = JSON.stringify(data);

        var myHeaders = new Headers();
        myHeaders.append("X-TENANT-ID", "az");
        myHeaders.append("Authorization", `Bearer ${token}`);

        var requestOptions = {
            method: 'POST',
            mode: 'cors', // no-cors, *cors, same-origin
            headers: myHeaders,
            body
        };

        return await fetch(url, requestOptions)
            .then(response => {
                return response.json()
            })
            .catch(error => {
                console.log('error', error)
                Promise.reject(error);
            });
    }


    function deleteBtn(id){
        if (!confirm("Seguro de borrar?")) {
            return;
        }
        
        const res = delete_row(id);

        res.then((resp) =>{            
            table.setData(api_url)
            .then(function(){
            })
            .catch(function(error){
                //handle error loading data
                console.log('error loading data');
            });
        })
    }

    async function delete_row(id) {
        const url = `${api_url}/${id}`;

        var myHeaders = new Headers();
        myHeaders.append("X-TENANT-ID", "az");
        myHeaders.append("Authorization", `Bearer ${token}`);

        var requestOptions = {
            method: 'DELETE',
            mode: 'cors', // no-cors, *cors, same-origin
            headers: myHeaders
        };

        return await fetch(url, requestOptions)
            .then(response => {
                return response.json()
            })
            .catch(error => {
                console.log('error', error)
                Promise.reject(error);
            });
    }

    function editBtn(data){
        console.log(data);
    }

    async function patch_row(id, data) {
        const url = `${api_url}/${id}`;

        let body = JSON.stringify(data);

        var myHeaders = new Headers();
        myHeaders.append("X-TENANT-ID", "az");
        myHeaders.append("Authorization", `Bearer ${token}`);

        var requestOptions = {
            method: 'PATCH',
            mode: 'cors', // no-cors, *cors, same-origin
            headers: myHeaders,
            body: body
        };

        return await fetch(url, requestOptions)
            .then(response => {
                return response.json()
            })
            .catch(error => {
                console.log('error', error)
                Promise.reject(error);
            });
    }


    /*
        Formatters disponibles para Tabulator:

        plaintext  -- es el mas generico-
        progress
        tickCross
        star
        list   -- requiere de editorParam

        Mas formaters:

        https://tabulator.info/docs/5.4/format

        Validacion: ver

        https://tabulator.info/docs/5.4/validate
    */

    window.addEventListener('DOMContentLoaded', (event) => {
        
        const render_datagrid = async () => {

            let columns = [];

            columns.push({
                //column definition in the columns array
                formatter: function(cell, formatterParams, onRendered) {
                    let row      = cell.getRow()
                    let data     = row.getData()
                    let id       = data.id;

                    let data_s   = JSON.stringify(data);

                    let input_id = "chk-"+id;

                    return `<input type="checkbox" id="${input_id}" onchange="checkboxSelected('${input_id}');"/>`;
                },
                width: 30,
                hozAlign: "center",
            })

            for (var field in defs) {
                let obj = {};
                let def = defs[field];

                obj.field = field;
                obj.title = typeof def.name == 'undefined' ? ucfirst(field) : def.name;
                obj.formatter = typeof def.formatter == 'undefined' ? 'plaintext' : def.formatter;
                obj.editor = true;

                columns.push(obj);
            }

            columns.push({
                //column definition in the columns array
                formatter: function(cell, formatterParams, onRendered) {
                    let row    = cell.getRow()
                    let data   = row.getData()
                    let id     = data.id;

                    let data_s = JSON.stringify(data);

                    const del_btn = `<button type="button" onclick="deleteBtn(${id})"><i class="fa fa-trash" style="color:#ff3333"></i></button>`;
                    const edt_btn = `<button type="button" onclick='editBtn(${data_s})'><i class="fa fa-pen" style="color:#6699ff"></i></button>`;

                    return `${edt_btn} ${del_btn}`;
                },
                width: 80,
                hozAlign: "center",
            })

            table = new Tabulator("#example-table", {
                ajaxURL: api_url,

                // ajaxConfig:"GET",
                // ajaxContentType:{
                //     headers:{
                //         'Content-Type': 'application/json',
                //     }
                // },

                //autoColumns:true,

                columns: columns,

                /*
                    https://tabulator.info/docs/5.4/select
                */
                //selectable:true,

                /* 
                    Formatea una columna
                */
                rowFormatter:function(row){                    
                    var data = row.getData();

                    if(data.deleted_at != null){
                        row.getElement().style.backgroundColor = "#ff3333";
                    }
                },

                ajaxParams: {
                    tenantid,
                    token
                },

                paginationSize:  50, // <--- puede implicar modificar el height
                paginationMode:  "remote",
                progressiveLoad: "scroll", // obligatorio?

                ajaxResponse: function(url, params, response) {
                    res.data = response.data[entity];
                    res.last_page = response.last_page;
                    return res;
                },

                reactiveData: true, //turn on data reactivity

                /*
                    Rendering
                */

                height: "325px",

                // layout: "fitData",
                // layout: "fitDataFill",
                // layout: "fitDataStretch",
                // layout: "fitDataTable",
                layout: "fitColumns",

                // responsiveLayout:"hide",
                responsiveLayout: "collapse",

                resizableColumnFit: true,
                placeholder: "Sin datos",

                // headerVisible:false, 
                // textDirection:"rtl",

            });

            //add row to bottom of table on button click
            // document.getElementById("btn-add").addEventListener("click", function() {
            //     table.addData([{
            //         id: 20,
            //         kilometraje: 550,
            //         num_asientos: 7
            //     }], false);
            // });

            table.on("tableBuilt", () => {
                // ...
            });

            /*
                Para edicion de campos puntuales
            */
            table.on("cellEdited", async (proxy) => {
                let initial_value = proxy._cell.initialValue;
                let old_value = proxy._cell.oldValue;
                let new_value = proxy._cell.value;

                if (new_value == old_value) {
                    return
                }

                let row = proxy.getData();
                let field_updated = proxy.getColumn()._column.field;

                data = {};

                let id = row.id;
                data[field_updated] = new_value;

                let res = await patch_row(id, data);
                //await console.log(res);
            });



        }; // end render_datadrid


        render_datagrid();

    });
</script>
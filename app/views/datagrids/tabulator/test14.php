<?php
    use simplerest\core\libs\HtmlBuilder\Bt5Form;
    use simplerest\core\libs\HtmlBuilder\Tag;

    Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
?>

<h3>Prueba con Ajax con paginacion</h3>

<script>
    const ucfirst = s => (s && s[0].toUpperCase() + s.slice(1)) || ""

    /*
        https://stackoverflow.com/questions/27746304/how-to-check-if-an-object-is-a-promise/27746324#27746324
    */
    function isPromise(p) {
        return p && Object.prototype.toString.call(p) === "[object Promise]";
    }
</script>

<div id="example-table"></div>

<?php
    echo tag('modal')->content(
        tag('modalDialog')->content(
            tag('modalContent')->content(
                tag('modalHeader')->content(
                    tag('modalTitle')->text('Modal title') . 
                    tag('closeButton')->dataBsDismiss('modal')
                ) .
                tag('modalBody')->content(
                    tag('p')->text('Modal body text goes here.')
                ) . 
                tag('modalFooter')->content(
                    tag('closeModal') .
                    tag('button')->text('Save changes')
                ) 
            ) 
        )
    )->id('exampleModal');

    echo tag('openButton')->target("exampleModal")->content('Launch demo modal')->class('my-3');
?>



<!--
    Para agregar botones al header ver (sin probar)

    https://stackoverflow.com/questions/67695811/tabulator-add-a-button-in-a-column-header
-->

<?php

$resource = "products";
$tenantid = "az";

/*
        Dado que no estoy usando un framework reactivo,
        las definiciones pueden directamente ofrecerse en el backend
        evitandome otro request.
    */

$defs   = get_defs($resource, $tenantid, false, false);

js_file('js/axios.min.js', null, true);

js("
    const resource = '" . $resource . "';
    const tenantid = '" . $tenantid . "';

    let   defs     = " . json_encode($defs) . ";
    ", null, true);
?>

<script>
    const token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url = `http://simplerest.lan/api/v1/${resource}`;

    let table   = {};
    let columns = [];
    let res     = {};

    //row selection formatter
    var rowSelectFormatter = function(cell, formatterParams, onRendered){
        var input = document.createElement("input");

        input.type  = "checkbox";
        input.name  = "test";
        //input.style = "margin: 0 20px 0 20px";

        return input;
    }

    defs.selectable = {title:" ", formatter:rowSelectFormatter}
   
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

        Mas formatters:

        https://tabulator.info/docs/5.4/format

        Validacion: ver

        https://tabulator.info/docs/5.4/validate
    */

    window.addEventListener('DOMContentLoaded', (event) => {
        
        const render_datagrid = async () => {

            let columns = [];
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
                    res.data = response.data[resource];
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
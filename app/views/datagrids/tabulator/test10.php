<h3>Prueba con Ajax con paginacion</h3>


<div>
    <button id="btn-add">(+)</button>
</div>

<div id="example-table"></div>

<!--
    Con Ajax

    Custom headers no estan siendo enviados asi que el tenant_id o que sea debe enviarse via parametro en la url !
-->

<?php
    $resource = "automoviles";
    $tenantid = "az";

    /*
        Dado que no estoy usando un framework reactivo,
        las definiciones pueden directamente ofrecerse en el backend
        evitandome otro request.
    */

    $defs   = get_defs($resource, $tenantid);

    js_file('js/axios.min.js', null, true);

    js("
        const defs     = " . json_encode($defs) . ";
        const resource = '". $resource . "';
        const tenantid = '". $tenantid . "';
    ", null, true);
?>

<script>
    const token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url  = `http://simplerest.lan/api/v1/${resource}`;

    let columns = [];
    let res     = {}

    const ucfirst = s => (s && s[0].toUpperCase() + s.slice(1)) || ""

    /*
        Formatters disponibles para Tabulator:

        input  -- es el mas generico-
        progress
        tickCross
        star
        list   -- requiere de editorParam
    */

    window.addEventListener('DOMContentLoaded', (event) => {
        const render_datagrid = async () => {
                    
            let columns = [];
            for (var field in defs){ 
                let obj = {};
                let def = defs[field];

                obj.field     = field;
                obj.title     = typeof def.name == 'undefined' ? ucfirst(field) : def.name;
                obj.formatter = typeof def.formatter == 'undefined' ? 'input' : def.formatter;
                obj.editor    = true;

                columns.push(obj);
            }

            var table = new Tabulator("#example-table", 
            {
                    ajaxURL:api_url,

                    // ajaxConfig:"GET",
                    // ajaxContentType:{
                    //     headers:{
                    //         'Content-Type': 'application/json',
                    //     }
                    // },

                    //autoColumns:true,

                    columns: columns,

                    ajaxParams: {
                        tenantid,
                        token
                    },

                    paginationSize:10,  // <--- puede implicar modificar el height
                    paginationMode:"remote", 
                    progressiveLoad:"scroll", // obligatorio?

                    ajaxResponse:function(url, params, response){                    
                        res.data      = response.data[resource];
                        res.last_page = response.last_page;
                        return res; 
                    },

                    reactiveData:true, //turn on data reactivity

                    /*
                        Rendering
                    */

                    height:"325px",

                    // layout: "fitData",
                    // layout: "fitDataFill",
                    // layout: "fitDataStretch",
                    // layout: "fitDataTable",
                    layout: "fitColumns",

                    // responsiveLayout:"hide",
                    responsiveLayout:"collapse",

                    resizableColumnFit:true,
                    placeholder:"Sin datos",

                    // headerVisible:false, 
                    // textDirection:"rtl",
                },
            );

            //add row to bottom of table on button click
            document.getElementById("btn-add").addEventListener("click", function(){
                table.addData([
                    {id:20, kilometraje: 550, num_asientos:7 }
                ], false);
            });

            table.on("tableBuilt", () => {
                // ...
            });

        }; // end render_datadrid

        render_datagrid();

    });
</script>
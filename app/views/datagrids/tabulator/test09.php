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
js_file('js/axios.min.js', null, true);
?>

<script>
    const resource = "automoviles";
    const tenantid = "az";
    const token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url = `http://simplerest.lan/api/v1/${resource}`;

    async function get_rest_definitions() {
        const url = `${api_url}?defs=1`;

        var myHeaders = new Headers();
        myHeaders.append("X-TENANT-ID", "az");
        myHeaders.append("Authorization", `Bearer ${token}`);

        var requestOptions = {
            method: 'GET',
            headers: myHeaders
        };

        return await fetch(url, requestOptions)
            .then(response => response.json())
            .catch(error => {
                console.log('error', error)
                //Promise.reject(new Error(400));
            });
    }

    let res = {};
    let defs; 

    window.addEventListener('DOMContentLoaded', (event) => {
        
        const render_datagrid = async () => {
            console.log('defs', defs); ///

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

                    columns:[
                        {title:"Id", field:"id", width:80},
                        {title:"Kilometraje", field:"kilometraje", editor:true},
                        {title:"Num de asientos", field:"num_asientos", sorter:"number", hozAlign:"left", editor:true},
                    ],

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

        (async () => {
            defs = await get_rest_definitions().then(function(res) {
                return res;
            });

            await render_datagrid();
        })();

    });
</script>
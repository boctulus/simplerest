<h3>Prueba con Ajax con paginacion</h3>


<div>
    <button id="reactivity-add">Add New Row</button>
    <button id="reactivity-delete">Remove Row</button>
    <button id="reactivity-update">Update First Row Name</button>
</div>

<div id="example-table"></div>

<!--
    Con Ajax

    Custom headers no estan siendo enviados asi que el tenant_id o que sea debe enviarse via parametro en la url !
-->

<script>
    const resource = "automoviles";
    const tenantid = "az"; 
    const token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url  = `http://simplerest.lan/api/v1/${resource}`; 

    res = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        var table = new Tabulator("#example-table", {
                ajaxURL:api_url,
                
                // ajaxConfig:"GET",
                // ajaxContentType:{
                //     headers:{
                //         'Content-Type': 'application/json',
                //     }
                // },
                autoColumns:true,

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
        document.getElementById("reactivity-add").addEventListener("click", function(){
            tabledata.push({name:"IM A NEW ROW", progress:100, gender:"male"});
        });

        //remove bottom row from table on button click
        document.getElementById("reactivity-delete").addEventListener("click", function(){
            tabledata.pop();
        });

        //update name on first row in table on button click
        document.getElementById("reactivity-update").addEventListener("click", function(){
            tabledata[0].name = "IVE BEEN UPDATED";
        });
        
        table.on("tableBuilt", () => {
            // ...
        });

    });

       
   
</script>
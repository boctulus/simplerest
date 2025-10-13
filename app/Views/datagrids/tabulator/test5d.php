<h3>Prueba con Ajax con paginacion</h3>

<div id="example-table"></div>

<!--
    Con Ajax

    Custom headers no estan siendo enviados asi que el tenant_id o que sea debe enviarse via parametro en la url !
-->

<script>
    const resource = "products";
    const tenantid = "az"; 
    const token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url  = `http://simplerest.lan/api/v1/${resource}`; 

    res = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        var table = new Tabulator("#example-table", {
                height:"325px",
                layout:"fitColumns",
                //responsiveLayout:"collapse",
                ajaxURL:api_url,
                
                // ajaxConfig:"GET",
                // ajaxContentType:{
                //     headers:{
                //         'Content-Type': 'application/json',
                //     }
                // },

                progressiveLoad:"scroll",
                paginationSize:10,
                placeholder:"No Data Set",
                //resizableColumnFit:true,
                autoColumns:true,

                ajaxParams: {
                    tenantid,
                    token
                },

                //paginationMode:"remote", //enable remote pagination
                // paginationSize:5, //  <------------ optional parameter to request a certain number of rows per page
                // paginationInitialPage:2, //<------------  optional parameter to set the initial page to load
            
                ajaxResponse:function(url, params, response){                    
                    res.data      = response.data[resource];
                    res.last_page = response.last_page;
                    return res; 
                },
            },
        );

        table.on("tableBuilt", () => {
            // ...
        });
    });

       
   
</script>
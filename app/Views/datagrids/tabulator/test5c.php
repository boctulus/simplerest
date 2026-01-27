<h3>Prueba con Ajax con paginacion</h3>

<div id="example-table"></div>

<!--
    Con Ajax

    Custom headers no estan siendo enviados asi que el tenant_id o que sea debe enviarse via parametro en la url !
-->

<script>
    // const resource = "products";
    // const tenantid = "az"; 
    // const token    = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY2NTAwMTM5NCwiZXhwIjoxNjc0MDAxMzk0LCJpcCI6IjEyNy4wLjAuMSIsInVzZXJfYWdlbnQiOiJQb3N0bWFuUnVudGltZVwvNy4yOS4yIiwidWlkIjoxLCJyb2xlcyI6W10sInBlcm1pc3Npb25zIjp7InRiIjpbXSwic3AiOltdfSwiaXNfYWN0aXZlIjoxLCJkYl9hY2Nlc3MiOltdfQ.XHCPxQ30xupsJCPuIVoMqWkjgni_zQy95S745BlCF8A";

    const api_url  = `http://simplerest.lan/fake/test5c`; 

    res = {};

    window.addEventListener('DOMContentLoaded', (event) => {
        var table = new Tabulator("#example-table", {
                height:"325px",
                layout:"fitColumns",

                ajaxURL:api_url,
                progressiveLoad:"scroll",
                paginationSize:10,
                placeholder:"No Data Set",

                autoColumns:true,
            
                
                ajaxResponse:function(url, params, response){
                    res = response; //
            
                    return response;
                },
            },
        );
    });

       
   
</script>
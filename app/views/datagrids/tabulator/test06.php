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
    
    window.addEventListener('DOMContentLoaded', (event) => {
        let tableData = [];

        var table = new Tabulator("#example-table", {
            height:"311px",
            layout:"fitColumns",
            responsiveLayout:"collapse",
            autoColumns:true,
            resizableColumnFit:true,
            persistence:{
                sort:true,
                filter:true,
                columns:true,
            },
            placeholder:"No Data Set",

            // AJAX
            ajaxURL:api_url, //ajax URL
            ajaxConfig:"GET",
            ajaxContentType:{
                headers:{
                    'Content-Type': 'application/json',
                }
            },

            ajaxParams: {
                tenantid,
                token
            },

            ajaxResponse:function(url, params, response){
                let res = {};
                
                res.data = response.data[resource];
                return res; 
            },

            progressiveLoad:"scroll",
            
            paginationMode:"remote", //enable remote pagination
            paginationSize:5, //  <------------ optional parameter to request a certain number of rows per page
            paginationInitialPage:2, //<------------  optional parameter to set the initial page to load

        });

        table.on("tableBuilt", () => {
            // table.setData(api_url)
            // .then(function(){
            //     //run code after table has been successfully updated
            //     console.log('table has been successfully updated');
            // })
            // .catch(function(error){
            //     //handle error loading data
            //     console.log('error loading data');
            // });
        });

    });

   
</script>
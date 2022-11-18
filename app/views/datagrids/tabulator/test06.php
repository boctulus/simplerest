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

            ajaxResponse:function(url, params, response){
                let data = response.data[resource];
                return response.data[resource]; 
            },

            ajaxParams: {
                tenantid,
                token
            },

            //progressiveLoad:"scroll",
            
            paginationMode:"remote", //enable remote pagination
            paginationSize:5, //  <------------ optional parameter to request a certain number of rows per page
            paginationInitialPage:2, //<------------  optional parameter to set the initial page to load

            dataSendParams:{
                "page":"page", 
                "size":"pageSize"
            } ,
            dataReceiveParams:{
                "last_page":"paginator.totalPages", // <----------  change last_page parameter name to "max_pages"
            } ,
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
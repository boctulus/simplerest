<h3>Prueba con Ajax (datos reales)</h3>

<div id="example-table"></div>

<!--
    Con Ajax
-->

<script>
    const api_url = "http://simplerest.lan/api/v1/part_numbers?tenantid=parts";
    
    window.addEventListener('DOMContentLoaded', (event) => {
        let tableData = [
        ];

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

            // AJAX
            ajaxURL:api_url, //ajax URL
            ajaxConfig:"GET",
            // ajaxContentType:{
            //     headers:{
            //         'Content-Type': 'application/json',
            //         'X-TENANT-ID' : 'parts'
            //     }
            // },
            //ajaxParams:{key1:"value1", key2:"value2"}, //ajax parameters

            ajaxResponse:function(url, params, response){
                return response.data.part_numbers; //pass the data array into Tabulator
            },
        });

        table.on("tableBuilt", () => {
            //table.import("json", ".json");
            table.setData(api_url)
            .then(function(){
                //run code after table has been successfully updated
                console.log('table has been successfully updated');
            })
            .catch(function(error){
                //handle error loading data
                console.log('error loading data');
            });
        });

    });

   
</script>
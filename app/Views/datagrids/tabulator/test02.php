<h3>Prueba con Ajax</h3>

<div id="example-table"></div>

<!--
    Con Ajax
-->

<script>
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
            ajaxURL:"http://simplerest.lan/fake/parts", //ajax URL
            ajaxConfig:"GET",
            // ajaxContentType:{
            //     headers:{
            //         'Content-Type': 'application/json',
            //         'X-TENANT-ID' : 'parts'
            //     }
            // },
            //ajaxParams:{key1:"value1", key2:"value2"}, //ajax parameters

            ajaxResponse:function(url, params, response){
                res_arr = JSON.parse(response);

                //url - the URL of the request
                //params - the parameters passed with the request
                //response - the JSON object returned in the body of the response.

                return res_arr; //pass the data array into Tabulator
            },
        });

        table.on("tableBuilt", () => {
            //table.import("json", ".json");
            table.setData("http://simplerest.lan/fake/parts")
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
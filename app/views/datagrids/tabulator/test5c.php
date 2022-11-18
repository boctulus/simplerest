<h3>Prueba con Ajax con paginacion</h3>

<div id="example-table"></div>

<!--
    Con Ajax

    Custom headers no estan siendo enviados asi que el tenant_id o que sea debe enviarse via parametro en la url !
-->

<script>
    const api_url  = `http://simplerest.lan/fake/test5c`; 
    window.addEventListener('DOMContentLoaded', (event) => {
        var table = new Tabulator("#example-table", {
                height:"311px",
                layout:"fitColumns",
                ajaxURL:api_url,
                progressiveLoad:"scroll",
                paginationSize:20,
                placeholder:"No Data Set",
                // columns:[
                //     {title:"Name", field:"name", sorter:"string", width:200},
                //     {title:"Progress", field:"progress", sorter:"number", formatter:"progress"},
                //     {title:"Gender", field:"gender", sorter:"string"},
                //     {title:"Rating", field:"rating", formatter:"star", hozAlign:"center", width:100},
                //     {title:"Favourite Color", field:"col", sorter:"string"},
                //     {title:"Date Of Birth", field:"dob", sorter:"date", hozAlign:"center"},
                //     {title:"Driver", field:"car", hozAlign:"center", formatter:"tickCross", sorter:"boolean"},
                // ],
                autoColumns:true,
            
                ajaxResponse:function(url, params, response){
                    console.log(response);
                    return response;
                },
            },
        );
    });

       
   
</script>
<h3>Prueba 01</h3>

<div id="example-table"></div>

<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        let tableData = [
            {"id":1, "name":"bob", "age":"23"},
            {"id":2, "name":"jim", "age":"45"},
            {"id":3, "name":"steve", "age":"32"}
        ];

        var table = new Tabulator("#example-table", {
            height:"130px",
            layout:"fitColumns",
            responsiveLayout:"collapse",
            autoColumns:true,
            resizableColumnFit:true,
            persistence:{
                sort:true,
                filter:true,
                columns:true,
            },
            // columns:[
            //     {title:"Nameee", field:"name"},
            //     {title:"Progress", field:"progress", sorter:"number"},
            //     {title:"Gender", field:"gender"},
            //     {title:"Rating", field:"rating"},
            //     {title:"Favourite Color", field:"col"},
            //     {title:"Date Of Birth", field:"dob", hozAlign:"center"},
            // ],
        });

        table.on("tableBuilt", () => {
            table.setData(tableData)
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
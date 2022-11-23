<h3>Prueba 01</h3>

<div id="example-table"></div>

<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        let tableData = [
            {"id":1, "name":"bob", "progress":"23", "rating":7, "gender":"m", "col":"blue"},
            {"id":2, "name":"jim", "progress":"45", "rating":10, "gender":"m", "col":"green"},
            {"id":3, "name":"steve", "progress":"32", "rating":6, "gender":"m", "col":"red"}
        ];

        var table = new Tabulator("#example-table", {
            height:"130px",
            layout:"fitColumns",
            responsiveLayout:"collapse",
            //autoColumns:true,
            resizableColumnFit:true,
            persistence:{
                sort:true,
                filter:true,
                columns:true,
            },
            columns:[
                {title:"Nameee", field:"name"},
                {title:"Progress", field:"progress", formatter:"progress"},
                {title:"Gender", field:"gender"},
                {title:"Rating", field:"rating"},
                {title:"Favourite Color", field:"col"},
                {title:"Date Of Birth", field:"dob", hozAlign:"center"},
            ],
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
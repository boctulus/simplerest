<h3>Prueba</h3>

<div id="example-table"></div>

<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        let tableData = [
            {id:1, name:"Billy Bob", age:"12", gender:"male", height:1, col:"red", dob:"", cheese:1},
            {id:2, name:"Mary May", age:"1", gender:"female", height:2, col:"blue", dob:"14/05/1982", cheese:true},
            {id:3, name:"Christine Lobowski", age:"42", height:0, col:"green", dob:"22/05/1982", cheese:"true"},
            {id:4, name:"Brendon Philips", age:"125", gender:"male", height:1, col:"orange", dob:"01/08/1980"},
            {id:5, name:"Margret Marmajuke", age:"16", gender:"female", height:5, col:"yellow", dob:"31/01/1999"},
            {id:6, name:"Billy Bob", age:"12", gender:"male", height:1, col:"red", dob:"", cheese:1},
            {id:7, name:"Mary May", age:"1", gender:"female", height:2, col:"blue", dob:"14/05/1982", cheese:true},
            {id:8, name:"Christine Lobowski", age:"42", height:0, col:"green", dob:"22/05/1982", cheese:"true"},
            {id:9, name:"Brendon Philips", age:"125", gender:"male", height:1, col:"orange", dob:"01/08/1980"},
            {id:10, name:"Margret Marmajuke", age:"16", gender:"female", height:5, col:"yellow", dob:"31/01/1999"},
        ];

        var table = new Tabulator("#example-table", {
            height:"311px",
            columns:[
                {title:"Name", field:"name"},
                {title:"Progress", field:"progress", sorter:"number"},
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
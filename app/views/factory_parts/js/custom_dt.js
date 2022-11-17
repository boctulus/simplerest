/*
    https://stackoverflow.com/questions/57321196/how-to-add-an-edit-buttons-in-datatables-plugin
*/

$(document).ready(function() {
    var data_use = [
        ["Garrett Winters", "Accountant", "Tokyo", "8422"],
        ["Ashton Cox", "Junior Technical Author", "San Francisco", "1562"],
        ["Cedric Kelly", "Senior Javascript Developer", "Edinburgh", "6224"],
        ["Airi Satou", "Accountant", "Tokyo", "5407"],
    ];

    var column_name = [{
        title: "table_1"
    }, {
        title: "table2"
    }, {
        title: "table3"
    }, {
        title: "table4"
    }];

    $('#parts_tb').DataTable({
        "sPaginationType" : "full_numbers",
        data : data_use,
        //columns : column_name,
        dom : 'Bfrtip',
        select : 'single',
        responsive : true,
        altEditor : true,
        //destroy : true,
        searching: false,
        buttons : [
            {
                extend : 'selected',
                text : 'Edit',
                name : 'edit'
            }
        ],
        
    });
});

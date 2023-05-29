
<table id="mainTable" data-toggle="table" data-classes="table table-striped">
    <thead>
        <tr>
            <th scope="col" data-field="USER_ID">User ID</th>
            <th scope="col" data-field="FIRST_NAME">First name</th>
            <th
                scope="col"
                data-field="LAST_NAME"
                data-editable="true"
                data-editable-type="text"
                data-editable-mode="inline"
                data-editable-emptytext="N/A"
            >Last name</th>
            <th scope="col" data-field="EMAIL">Email</th>
            <th scope="col" data-field="COUNTRY">Country</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>1</td><td>John</td><td>Smith</td><td>john.smith@example.com</td><td>US</td></tr>
        <tr><td>2</td><td>Jane</td><td>Doe</td><td>jane.doe@example.com</td><td>ES</td></tr>
        <tr><td>3</td><td>Alice</td><td></td><td>alice@example.com</td><td>FR</td></tr>
        <tr><td>4</td><td>Bob</td><td>Jones</td><td>bjonesss@example.com</td><td>UK</td></tr>
    </tbody>
</table>

<script>
    onLoaded((event)=> {
        $("#mainTable").on("editable-save.bs.table", function(event, field, row, rowIndex, oldValue, el) {
            alert("New value = " + row[field] + ", old value = " + oldValue);
        });
    });
</script>

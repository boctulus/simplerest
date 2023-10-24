<style>
    table
    {
        width: auto;
        font: 17px Calibri;
    }
    table, th, td 
    {
        border: solid 1px #DDD;
        border-collapse: collapse;
        padding: 2px 3px;
        text-align: center;
    }
    img 
    {
        width: 50%;
    }
</style>

<div>
    <table id="tab"> 
        <tr>
            <th>Bird Name</th>
                <th>Scientific Name</th>
                    <th>Image</th>
        </tr>
        <tr>
            <td>Bald Eagle</td>
                <td>Haliaeetus leucocephalus</td>
                    <td><img src="https://www.encodedna.com/images/theme/bald-eagle.png" alt="Bald Eagle" /></td>
        </tr>
        <tr>
            <td>Morning Dove</td>
                <td>Zenaida macroura</td>
                    <td><img src="https://www.encodedna.com/images/theme/morning-dove.png" alt="Morning Dove" /></td>
        </tr>
    </table>
</div>

<p>
    <input type="button" value="Print Table" onclick="myApp.printTable()" />
</p>

<script>
    var myApp = new function () {
        this.printTable = function () {
            var tab = document.getElementById('tab');

            var style = "<style>";
                style = style + "table {width: 100%;font: 17px Calibri;}";
                style = style + "table, th, td {border: solid 1px #DDD; border-collapse: collapse;";
                style = style + "padding: 2px 3px;text-align: center;}";
                style = style + "</style>";

            var win = window.open('', '', 'height=700,width=700');
            win.document.write(style);          //  add the style.
            win.document.write(tab.outerHTML);
            win.document.close();
            win.print();
        }
    }
</script>
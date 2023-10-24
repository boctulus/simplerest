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

<h1>Titulo de la pagina</h1>

<h3>Subtitulo</h3>

<div id="printable">
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
    <input type="button" value="Print Table" onclick="jQuery('#printable').printThis()" />
</p>

<footer>
    Creado por Pablo
</footer>

<script>
    
</script>
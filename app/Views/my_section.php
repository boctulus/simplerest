<h3>My Section</h3>

<span>Passed: <?= $my_var ?></span>

<hr />

<div style="height: 50px;"></div>

<?php
     js('
     setTimeout(() => {
         $("h1").attr("style", "color: blue;");
         $("body").attr("style", "background-color: grey");
     }, "2000");
    ');
?>


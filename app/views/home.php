<?php
    js_file('\assets\js\more_tests\console_log.js', [
        'async' => true  // aun sin implementar
    ], true);

    js_file('\assets\js\more_tests\coloured_body.js');
    js_file('\assets\js\more_tests\coloured_h1.js');

    css_file('\assets\css\more_tests\span_color.css');

    css('
        hr {
            border: none;
            height: 10px;
            background-color: red;
        }
    ');

    js('
        setTimeout(() => {
            $("h1").attr("style", "color: blue;");
            $("body").attr("style", "background-color: grey");
        }, "2000");
    ');
?>

<h1>Home Page</h1>

<br/>
<p>Bienvenido</p>

<?php
    $my_var = 'some value..'; 
    section('my_section.php', [ 'my_var' => $my_var ]);
?>
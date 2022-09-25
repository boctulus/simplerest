<?php
    use simplerest\core\View;

    View::enqueue_js('\assets\js\more_tests\console_log.js', null, true);
    View::enqueue_js('\assets\js\more_tests\coloured_body.js');
    View::enqueue_js('\assets\js\more_tests\coloured_h1.js');
?>

<h1>Home Page</h1>

<br/>
<p>Bienvenido</p>

<?php

    $my_var = 'some value..'; 

    section('my_section.php',
        [
            'my_var' => $my_var
        ]
    );
?>
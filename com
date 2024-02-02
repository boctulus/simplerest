#!/usr/bin/env php
<?php

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

    use simplerest\core\FrontController;

    /*
       Parse command line arguments into the $_GET variable <sep16@psu.edu>
    */

    parse_str(implode('&', array_slice($argv, 3)), $_GET);

       
    FrontController::resolve();
 	




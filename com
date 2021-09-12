#!/usr/bin/env php
<?php

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR. 'app.php';

    use simplerest\core\FrontController;

       
    FrontController::resolve();
 	




<?php

use Boctulus\Simplerest\Core\WebRouter;


WebRouter::get('auth/login', function(){
    view('userlogin/login', ['title' => 'Login'], 'templates/tpl.php');
});

WebRouter::get('auth/rememberme', function(){
    view('userlogin/rememberme', ['title' => 'Recordar contraseña'], 'templates/tpl.php');
});
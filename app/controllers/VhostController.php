<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;

/*
    Este controlador podria ser parte del comando make
    aunque tal vez tampoco tenga tanto sentido

    Obvio es mejor usar Docker
*/

class VhostController extends MyController
{
    protected $www_dir         = 'D:\\www\\';
    protected $vhost_conf_path = 'D:\\wamp64\\bin\\apache\\apache2.4.51\\conf\\extra\\httpd-vhosts.conf';
    protected $hosts_path      = 'C:\Windows\\System32\\drivers\\etc\\hosts';

    function make_dir(string $name){
        if (!is_dir("{$this->www_dir}{$name}")){
            dd("Creating directory {$this->www_dir}{$name}");
          
            Files::mkDirOrFail("{$this->www_dir}{$name}");
        } 
    }

    function create(string $name, string $extension = 'test', int $port = 80){
        /*
            Seria util poder remover linea a linea, la misma cantidad de espacios desde la primera linea no-vacia

            Strings::tabulate() solo agrega tabs
        */
        
        $vhost_template = "
        <VirtualHost *:{$port}>
            ServerName {$name}.{$extension}
            DocumentRoot \"{$this->www_dir}{$name}\"

            ServerAlias {$name}.{$extension}

            <Directory \"{$this->www_dir}{$name}\">
                DirectoryIndex index.htm index.html index.php
                AllowOverride All
                Order allow,deny
                Allow from all
                Options +Indexes +Includes +FollowSymLinks +MultiViews
                Require local
            </Directory>
        </VirtualHost>
        ";

        if (!Strings::contains("{$name}.{$extension}", file_get_contents($this->vhost_conf_path))){
            Files::appendOrFail($this->vhost_conf_path, PHP_EOL . $vhost_template);

            dd("Virtual host \"{$name}.{$extension}\" added");
        }

        /*
            Hosts file
        */

        $new_line = "127.0.0.1	{$name}.{$extension}";

        if (!Strings::contains("{$name}.{$extension}", file_get_contents($this->hosts_path))){
            //Files::appendOrFail($this->hosts_path, PHP_EOL . $new_line);
            //dd("Domain \"{$name}.{$extension}\" added to hosts file");

            dd("Please edit \"{$this->hosts_path}\" and add \"$new_line\"");
            dd("Then, ... restart Apache");
        }
        
       
    }
}


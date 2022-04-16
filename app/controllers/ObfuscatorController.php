<?php

namespace simplerest\controllers;

use PhpParser\Node\Scalar\MagicConst\Dir;
use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Strings;

class ObfuscatorController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index(){}

    /*
        Instalar la extensión de php

        sudo apt install php-yaml

        o versiones en específico

        sudo apt install php7.4-yaml
        sudo apt install php8.1-yaml
    */
    function fromdir(string $ori)
    {
        $yaml_file = Strings::removeTrailingSlash($ori) . DIRECTORY_SEPARATOR . 'obf.yaml';
        $yaml_str  = file_get_contents($yaml_file);

        if (!function_exists('yaml_parse') && !isset(get_loaded_extensions()['yaml'])){
            throw new \Exception("Extension yaml not installed");
        }

        d(
            yaml_parse($yaml_str)
        );
    }
}


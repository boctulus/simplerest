<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Obfuscator;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\Files;
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

        if (!file_exists($yaml_file)){
            throw new \Exception("File '$yaml_file' not found");
        }

        $yaml_str  = file_get_contents($yaml_file);

        if (!function_exists('yaml_parse') && !isset(get_loaded_extensions()['yaml'])){
            throw new \Exception("Extension yaml not installed");
        }

        $arr = yaml_parse($yaml_str);

        if (!isset($arr['dest'])){
            throw new \Exception("dest in yaml is required");
        }

        $dest        = Files::getAbsolutePath(Files::isAbsolutePath($arr['dest']) ? $arr['dest'] : $ori . DIRECTORY_SEPARATOR . $arr['dest']);
        $excluded    = Arrays::shift($arr, 'excluded', []);
        $def_profile = Arrays::shift($arr, 'profile');

        if ($dest === false){
            throw new \Exception("Invalid path '$dest'");
        }

        unset($arr['dest']);

        /*
            Grupos
        */
        foreach ($arr as $group => $props){
            dd(strtoupper($group) . "----------------------------------------------------\r\n");
            dd($props, strtoupper($group));
            dd($def_profile, strtoupper($group));

            $files   = $props['files']   ?? [];  
            $options = $props['options'] ?? [];
            $profile = $props['profile'] ?? $def_profile;

            $ok = Obfuscator::obfuscate($ori, $dest, $files, null, $options, $profile, false);
            d($ok);

            /*
                Los archivos procesados en un grupo podrían ser excluidos de los siguientes
            */
            
            $excluded = array_merge($excluded, $files);

            dd("--------------------------------x-------------------------------------\r\n\r\n\r\n");
        }

        /*
            Sin grupo
        */
        $ok = Obfuscator::obfuscate($ori, $dest, null, $excluded, null, $def_profile, false);
        d($ok);
    
    }
}


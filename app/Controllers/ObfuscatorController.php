<?php

namespace Boctulus\Simplerest\Controllers;

use PhpParser\Node\Scalar\MagicConst\Dir;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Obfuscator;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;

class ObfuscatorController extends Controller
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
        $_excluded = [];

        $ori_non_trailing_slash = Files::removeTrailingSlash($ori);
        $yaml_file = $ori_non_trailing_slash . DIRECTORY_SEPARATOR . 'obf.yaml';

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

            dd("--------------------------------x-------------------------------------\r\n\r\n\r\n");
        }

        $ori_files = Files::removePath(
            Files::deepScan($ori, false), $ori
        );

        $dst_files = Files::removePath(
            Files::deepScan($dest, false), $dest
        );

        // dd($ori_files);
        // dd($dst_files);

        // Elimino la carpeta .git de los resultados
        // $_excluded = array_merge($excluded, 
        //     Files::removePath(
        //         Files::deepScan($ori_non_trailing_slash . DIRECTORY_SEPARATOR . '.git', false), $ori)
        // );

        $files = array_diff($ori_files, $dst_files, $_excluded);

        /*
            Sin grupo
        */

        $tmp  = Files::tempDir();
        $_dst = "$tmp/to_obsfuscate_no_group";

        // Deltree sobre $_dst
        Files::mkDir  ($_dst);
        Files::delTree($_dst);

        // Copio archivos a ofuscar en $_dst
        Files::copy($ori, $_dst, $files);

        // Ofusco 
        Obfuscator::obfuscate($_dst, $dest, null, $excluded, null, $def_profile, false);
        
        // copio archivos excluidos de la ofuscación
        Files::copy($ori, $dest, $excluded);
   
        // dd($excluded, 'EXCLUDED');
        // dd($files, 'FILES');
    }
}


<?php

namespace Boctulus\Simplerest\Core\Traits;

use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Libs\Strings;

trait PagesTrait
{
    /*
        Carga cualquier pagina dentro app/pages

        y puede estar dentro de carpetas.

        Ej:

        /admin/some_folder/other
    */
    function __call($name, $args = null)
    {  
        $slugs = Url::getSlugs(Url::currentUrl());
        $slugs = array_slice($slugs, 0, count($slugs) - count($args) );

        if (count($slugs) === 0){
            throw new \Exception("The page name is missing");
        }

        $namespace  = implode('\\', array_slice($slugs, 0, count($slugs)-1));

        $class_name = Strings::snakeToCamel($slugs[count($slugs)-1]);
        $class_name = "simplerest\\pages\\{$namespace}\\$class_name";

        if (!class_exists($class_name)){
            throw new \Exception("Class '$class_name' not found");
        }

        $instance = new $class_name();
       
        $content = $instance->index(...$args);

        render($content,  $this->tpl ?? $instance->tpl, array_merge($this->tpl_params, $instance->tpl_params)); 
    }

    function index(){
        /*
            Esto no tendria sentido con el router (solo con el frontController)
            y por eso se llama $extra y puede ser nulo
        */

        $ctrl     = Strings::lastSegmentOrFail(__CLASS__, '\\');
        $ctrl_seg = Strings::beforeLast($ctrl, 'Controller');
        $ctrl_seg = strtolower($ctrl_seg);
        $extra    = !empty($ctrl_seg) ? "$ctrl_seg\\" : "";
        
        $default_page = ucfirst(str_replace('/', '\\', $this->default_page));

        $class_name   = "simplerest\\pages\\{$extra}{$default_page}";;

        $instance     = new $class_name();

        $content      = $instance->index();

        render($content,  $this->tpl ?? $instance->tpl, array_merge($this->tpl_params, $instance->tpl_params));    
    }
}

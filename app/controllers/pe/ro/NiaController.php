<?php

namespace simplerest\controllers\pe\ro;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\Url;
use simplerest\core\Response;
use simplerest\pages\Tabulator;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;


class NiaController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function __call($name, $arguments)
    {   
        $slugs = Url::getSlugs(Url::currentUrl());

        //dd($slugs, $name);

        $namespace  = implode('\\', array_slice($slugs, 0, count($slugs)-1));

        $path       = PAGES_PATH . $namespace . DIRECTORY_SEPARATOR;

        $class_name = Strings::snakeToCamel($slugs[count($slugs)-1]);
        $file       = $class_name . ".php";

        $full       = $path . $file;

        $qualified_class_name = "simplerest\\pages\\{$namespace}\\$class_name";

        if (!class_exists($qualified_class_name)){
            throw new \Exception("Class '$qualified_class_name' not found");
        }

        // dd($qualified_class_name);

        $instance = new $class_name();

        return $instance->index();
    }
}


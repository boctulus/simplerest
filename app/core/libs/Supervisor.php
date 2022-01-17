<?php declare(strict_types=1);

namespace simplerest\core\libs;

class Supervisor
{
    function __construct() {
        $this->start();
    }

    protected function start(){
        foreach (new \FilesystemIterator(CRONOS_PATH) as $fileInfo) {
            if($fileInfo->isDir() || $fileInfo->getExtension() != 'php') continue;

            $filename   = CRONOS_PATH . $fileInfo->getFilename();
            $class_name = Strings::getClassNameByFileName($filename);

            require_once $filename;

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' doesn't exist in $file");
            } 

            $instance = new $class_name();
        }   
    }

}


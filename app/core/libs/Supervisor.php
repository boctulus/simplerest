<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\System;

class Supervisor
{
    protected Array $filenames;
    protected Array $classes;
    protected Array $freq;
    protected Array $pids;  // usar para matar los cronos ! 

    function __construct() {
        $this->scan();

        foreach ($this->classes as $ix => $class){
            System::runInBackground("php com async loop {$this->filenames[$ix]}");
        }
    }

    protected function scan(){
        foreach (new \FilesystemIterator(CRONOS_PATH) as $fileInfo) {
            if($fileInfo->isDir() || $fileInfo->getExtension() != 'php') continue;

            $filename   = CRONOS_PATH . $fileInfo->getFilename();
            $class_name = Strings::getClassNameByFileName($filename);

            require_once $filename;

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' doesn't exist in $file");
            } 

            $this->classes[]   = $class_name;
            $this->filenames[] = basename($filename);
        }   
    }

}


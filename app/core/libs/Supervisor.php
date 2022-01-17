<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\System;

class Supervisor
{
    protected Array $filenames;
    protected Array $classes;
    protected Array $freq;

    function __construct() {
        $this->scan();
        // d($this->filenames);
        // d($this->freq);

        foreach ($this->classes as $ix => $class){
            if (!class_exists($this->classes[$ix])){
                require_once $this->filenames[$ix];
            }    

            $instance = new $this->classes[$ix]();

            //System::runInBackground(
            $this->loop($instance, $this->freq[$ix]['second']);
        }
    }

    protected function loop(BackgroundService $task, int $every_seconds){
        //System::runInBackground()
        while (true){
            $task->start();
            sleep($every_seconds);
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
            $this->filenames[] = $filename;

            $this->freq[] = $class_name::getFrequency();
        }   
    }

}


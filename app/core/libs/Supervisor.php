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
            $task = $this->filenames[$ix];
            $mnth = $this->freq[$ix]['month']    ?? -1;
            $mndy = $this->freq[$ix]['monthday'] ?? -1;
            $wkdy = $this->freq[$ix]['weekday']  ?? -1;
            $hour = $this->freq[$ix]['hour']     ?? -1;
            $mins = $this->freq[$ix]['minute']   ??  0;
            $secs = $this->freq[$ix]['second']   ??  0;

            d("php com async loop $task $mnth $mndy $wkdy $hour $mins $secs");
            System::runInBackground("php com async loop $task $mnth $mndy $wkdy $hour $mins $secs");
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

            $this->freq[] = $class_name::getFrequency();
        }   
    }

}


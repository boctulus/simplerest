<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\System;

class Supervisor
{
    protected Array $filenames;
    protected Array $classes;
    protected Array $freq;

    function __construct() {
        $this->stop();

        $this->scan();

        DB::getDefaultConnection();

        DB::table('background_process')
        ->truncate();

        foreach ($this->classes as $ix => $class){
            $dont_overlap = $class::canOverlap();

            if ($dont_overlap && Supervisor::isRunning($this->filenames[$ix])){
                d("Aborting for ". __FILE__);
                return;
            }

            $pid = System::runInBackground("php com async loop {$this->filenames[$ix]}", 'logs/output.txt');
            
            // lo ideal es poder elegir el "driver" ya sea en base de datos o en memoria tipo REDIS para los PIDs
            
            DB::table('background_process')
            ->insert([
                'job_file' => $this->filenames[$ix],
                'pid' => $pid
            ]);
        }
    }

    static function isRunning(string $job_file) : bool {
        return DB::table('background_process')
        ->where(['job_file' => $job_file])
        ->exists();
    }

    static function stop(){
        DB::getDefaultConnection();

        $pids = DB::table('background_process')
        ->pluck('pid');

        if (empty($pids)){
            return;
        }

        foreach ($pids as $pid){
            $exit_code = shell_exec("kill $pid 2>/dev/null && echo $?");

            if ($exit_code == 0){
                DB::table('background_process')
                ->where(['pid' => $pid])
                ->delete();
            }
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


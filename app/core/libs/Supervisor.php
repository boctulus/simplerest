<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\System;

class Supervisor
{
    static protected Array $filenames;
    static protected Array $classes;
    static protected Array $freq;

    static function start() {
        static::stop();
        sleep(1);
        static::scan();

        DB::getDefaultConnection();

        DB::table('background_process')
        ->truncate();

        foreach (static::$classes as $ix => $class){
            if (!$class::isActive()){
                continue;
            }

            $file = static::$filenames[$ix];
            $pid  = System::runInBackground("php com async loop $file", 'logs/output.txt');
            
            // lo ideal es poder elegir el "driver" ya sea en base de datos o en memoria tipo REDIS para los PIDs
            
            DB::table('background_process')
            ->insert([
                'job' => $file,
                'pid' => $pid
            ]);
        }
    }

    static function isRunning(string $job) : bool {
        DB::getDefaultConnection();

        return DB::table('background_process')
        ->where(['job' => $job])
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
            $exit_code = System::kill($pid);
    
            if ($exit_code == 0){
                DB::table('background_process')
                ->where(['pid' => $pid])
                ->delete();
            }
        }
    }    

    static protected function scan(){
        foreach (new \FilesystemIterator(CRONOS_PATH) as $fileInfo) {
            if($fileInfo->isDir() || $fileInfo->getExtension() != 'php') continue;

            $filename   = CRONOS_PATH . $fileInfo->getFilename();
            $class_name = Strings::getClassNameByFileName($filename);

            require_once $filename;

            if (!class_exists($class_name)){
                throw new \Exception ("Class '$class_name' doesn't exist in $file");
            } 

            static::$classes[]   = $class_name;
            static::$filenames[] = basename($filename);
        }   
    }

}


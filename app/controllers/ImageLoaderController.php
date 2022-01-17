<?php

namespace simplerest\controllers;

use simplerest\core\controllers\ConsoleController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Time;

/*
    https://tutorialspage.com/benchmarking-on-the-glob-and-readdir-php-functions/
*/
class ImageLoaderController extends ConsoleController
{
    function __construct()
    {
        parent::__construct();
    }

    private $directory = 'app/migrations/compania/';

    function index(){
        $t = Time::exec(function(){
            $this->runreaddir();
        });

        d($t, 'ReadDir');


        $t = Time::exec(function(){
            $this->runscandir();
        });

        d($t, 'ScanDir');


        $t = Time::exec(function(){
            $this->runglob();
        });

        d($t, 'Glob');

        $t = Time::exec(function(){
            $this->runiterator();
        });

        d($t, 'Iterator');
    }

    function test1(){
        $t = Time::exec(function(){
            $this->runreaddir();
        });

        d($t, 'ReadDir');
    }

    function test2(){
        $t = Time::exec(function(){
            $this->runscandir();
        });

        d($t, 'ScanDir');
    }

    function test3(){
        $t = Time::exec(function(){
            $this->runglob();
        });

        d($t, 'Glob');
    }

    function test4(){
        $t = Time::exec(function(){
            $this->runiterator();
        });

        d($t, 'Iterator');
    }

    private function runiterator()
    {
        $results = array();
        foreach (new \DirectoryIterator($this->directory) as $fileInfo) {
            $results[] = $fileInfo;
        }
        return $results;
    }
    
    private function runreaddir()
    {
        $results = array();
        if ($handle = opendir($this->directory)) {
            while (false !== ($filename = readdir($handle))) {
                $results[] = $filename;
             }
        }
        return $results;
    }
    
    private function runscandir()
    {
        $results = scandir($this->directory);
        return $results;
    }
    
    private function runglob()
    {
        $results = glob($this->directory . '/*', GLOB_NOSORT);
        return $results;
    }
}


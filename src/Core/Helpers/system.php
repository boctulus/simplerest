<?php

use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\System;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\ApacheWebServer;


/*
    Ej:

    bg_com("bzz_import do_process")

    Parecido a System::com() pero corre en background
*/
function bg_com(string $cmd, bool $debug = false){
    $file_path  = System::getPHP();
    $dir        = ROOT_PATH;
    $args       = "{$dir}com $cmd";

    if ($debug){
        dd("$file_path $args", 'CMD');
    }

    $pid = System::runInBackground($file_path, $dir, $args);

    return $pid;
}

function is_cli(){
	return (php_sapi_name() == 'cli');
}

function is_unix(){
	return (DIRECTORY_SEPARATOR === '/');
}

function set_server_limits($upload_max_filesize = '1024M', $post_max_size = '1024M', $memory_limit = '768M', $max_exec_time = '600'){
    $config = Config::get();

    /*
        Si no funciona, debe modificarse el php.ini
    */

    ApacheWebServer::updateHtaccessFile([
        'upload_max_filesize' => $upload_max_filesize,
        'post_max_size'       => $post_max_size,
    ], ROOT_PATH);

    @ini_set("memory_limit", $memory_limit ?? $config["memory_limit"] ?? "768M");
    @ini_set("max_execution_time", $max_exec_time ?? $config["max_execution_time"] ?? "600");
}

function get_server_limits(){
    return [
        "upload_max_filesize"   => ini_get("upload_max_filesize"),
        "post_max_size"         => ini_get("post_max_size"),
        "memory_limit"          => ini_get("memory_limit"),
        "max_execution_time"    => ini_get("max_execution_time"),
    ];
}

function long_run(){
	System::setMemoryLimit('1024M');
	System::setMaxExecutionTime(-1);
}


/*
	Tiempo en segundos de sleep

	Acepta valores decimales. Ej: 0.7 o 1.3
*/
function nap($time, $echo = false){
	if ($echo){
		StdOut::print("Taking a nap of $time seconds");
	}

	if (!is_numeric($time)){
		throw new \InvalidArgumentException("Time should be a number");
	}

	$time = ((float) ($time)) * 1000000;

	return usleep($time);	 
}
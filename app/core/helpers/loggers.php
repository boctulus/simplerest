<?php

use simplerest\core\libs\Files;
use simplerest\core\libs\Logger;


/*
    Requiere que este habilitado el modo debug
*/
function logger($data, ?string $path = null, $append = true){
    if (!config()['debug']){
        return;
    }

    return Logger::log($data, $path, $append);
}

/*
    Requiere que este habilitado el modo debug
*/
function dump($object, ?string $path = null, $append = false){
    if (!config()['debug']){
        return;
    }

    return Files::dump($object, $path, $append);
}

/*
    Requiere que este habilitado el modo debug
*/
function log_error($error){
    if (!config()['debug']){
        return;
    }

    return Logger::logError($error);
}

/*
    Requiere que este habilitado el modo debug y log_sql
*/  
function log_sql(string $sql_str){
    if (!config()['debug'] || !config()['log_sql']){
        return;
    }

    return Logger::logSQL($sql_str);
}

function dd_log(){
    if (!is_cli()) echo '<pre>';
    echo file_exists(LOGS_PATH . 'log.txt') ? file_get_contents(LOGS_PATH . 'log.txt') : '--x--';
    if (!is_cli()) echo '</pre>';
}

function dd_error_log(){
    if (!is_cli()) echo '<pre>';
    echo file_exists(LOGS_PATH . 'errors.txt') ? file_get_contents(LOGS_PATH . 'errors.txt') : '--x--';
    if (!is_cli()) echo '</pre>';
}

function kill_logs(){
    if(file_exists(LOGS_PATH . 'errors.txt')){
        unlink(LOGS_PATH . 'errors.txt');
        dd("File 'errors.txt' was deleted");
    }

    if(file_exists(LOGS_PATH . 'log.txt')){
        unlink(LOGS_PATH . 'log.txt');
        dd("File 'log.txt' was deleted");
    }
}
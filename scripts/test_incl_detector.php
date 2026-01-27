<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error ($errno): $errstr in $errfile on line $errline");
});

class FileIncludeStream {
    protected $position;
    protected $varname;
    protected $handle;
    protected $buffer;    
    const     LOGS_PATH = __DIR__ . '/logs/';

    public function stream_open($path, $mode, $options, &$opened_path) {
        try {
            if (!file_exists(static::LOGS_PATH)) {
                mkdir(static::LOGS_PATH, 0777, true);
            }

            // Quitamos el prefijo 'include://'
            $realPath = str_replace('include://', '', $path);
            
            // Verificar si el archivo existe
            if (!file_exists($realPath)) {
                trigger_error("File not found: $realPath", E_USER_WARNING);
                return false;
            }

            // Abrir el archivo
            $this->handle = fopen($realPath, $mode);
            if ($this->handle === false) {
                return false;
            }

            // Registrar en el log
            $timestamp = date('Y-m-d H:i:s');
            $logEntry  = "Included file: $realPath at $timestamp\n";
            static::log($logEntry);

            $this->position = 0;
            
            return true;
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return false;
        }
    }

    public function stream_read($count) {
        $ret = fread($this->handle, $count);
        if ($ret) {
            $this->position += strlen($ret);
        }
        return $ret;
    }

    public function stream_write($data) {
        $ret = fwrite($this->handle, $data);
        if ($ret) {
            $this->position += $ret;
        }
        return $ret;
    }

    public function stream_tell() {
        return $this->position;
    }

    public function stream_eof() {
        return feof($this->handle);
    }

    public function stream_seek($offset, $whence) {
        $ret = fseek($this->handle, $offset, $whence);
        if ($ret === 0) {
            $this->position = ftell($this->handle);
            return true;
        }
        return false;
    }

    public function stream_stat() {
        return fstat($this->handle);
    }

    public function url_stat($path, $flags) {
        $realPath = str_replace('include://', '', $path);
        return stat($realPath);
    }

    public function stream_close() {
        return fclose($this->handle);
    }

    static function log($logEntry){
        file_put_contents(static::LOGS_PATH . 'includes.txt', $logEntry, FILE_APPEND);
    }
}

// Desregistrar el wrapper si ya existe
if (in_array('include', stream_get_wrappers())) {
    stream_wrapper_unregister('include');
}

// Registrar el nuevo wrapper
stream_wrapper_register('include', 'FileIncludeStream');



try {
    // FileIncludeStream::log('TEST');

    require __DIR__ . '/scripts/test.php';
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}



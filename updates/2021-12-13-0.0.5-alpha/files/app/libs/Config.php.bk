<?php

namespace simplerest\libs;

class Config implements \ArrayAccess
{
    protected $data;
    
    function __construct() { 
        $this->data = include CONFIG_PATH . 'config.php';
    }

   function get(string $key = null){
        if ($key == null){
            return $this->data;
        } else {
            return $this->data[$key];
        }
    }

    function set(string $key, $val){
        $this->data[$key] = $val;
    }

    /* ArrayAccess interface */

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    
}
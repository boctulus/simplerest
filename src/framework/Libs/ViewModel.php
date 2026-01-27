<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

/*
    https://claude.ai/chat/2da02df7-b710-49b0-b42a-17a435233b49
    
    Clase propuesta por Claude

    "View" deberia ser el equivalente a "Activity" en Android o sea para "pantalla completa"
    "Fragment" seria igual que en Android

    Y faltaria crear "UIController" que seria el "Controller" de M-VM-C (MVMC)
*/

class ViewModel 
{
    protected $data = [];
    protected $observers = [];
    
    public function setValue($key, $value) {
        $this->data[$key] = $value;
        $this->notifyObservers($key);
    }
    
    public function getValue($key) {
        return $this->data[$key] ?? null;
    }
    
    public function observe($key, $callback) {
        if (!isset($this->observers[$key])) {
            $this->observers[$key] = [];
        }
        $this->observers[$key][] = $callback;
    }
    
    protected function notifyObservers($key) {
        if (isset($this->observers[$key])) {
            foreach ($this->observers[$key] as $callback) {
                call_user_func($callback, $this->data[$key]);
            }
        }
    }
}   


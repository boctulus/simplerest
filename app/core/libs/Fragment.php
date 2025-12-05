<?php

namespace Boctulus\Simplerest\core\libs;

use Boctulus\Simplerest\Core\Libs\Strings;

/*
    Fragment LifeCycle

    onCreate() - Cuando se inicializa el componente
    onAttach() - Cuando se añade al DOM
    onResume() - Cuando se vuelve visible/activo
    onPause() - Cuando pierde el foco pero sigue en memoria
    onDetach() - Cuando se elimina del DOM
    onDestroy() - Cuando se destruye completamente
*/
abstract class Fragment 
{
    protected $viewModel;
    protected $parent;
    
    public function onCreate() { /* ... */ }
    public function onAttach($parent) { 
        $this->parent = $parent;
        /* ... */ 
    }
    public function onResume() { /* ... */ }
    public function onPause() { /* ... */ }
    public function onDetach() { /* ... */ }
    public function onDestroy() { /* ... */ }
    
    abstract public function render();
}

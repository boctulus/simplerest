<?php

namespace Boctulus\Simplerest\Pages\Admin;

use Boctulus\Simplerest\abstracts\pages\Page;

class Calc extends Page
{
    public $tpl_params = [
        'title'      => 'Calc',
        'page_name'  => 'Calculator'
    ];

    function index($op, ...$args){
        $this->tpl_params['page_name'] = "Operacion ".ucfirst($op);

        switch ($op){
            case 'sum':
               return "la suma de [ ". implode(', ', $args) . ' ] es '. array_sum($args);
            case 'mul':
                return "el producto de [ ". implode(', ', $args) . ' ] es '. array_product($args);
            default:
                $res = 'Invalid operation for '. $op;
        }
        
        return $res;
    }
}
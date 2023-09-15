<?php

namespace simplerest\core;

use simplerest\core\libs\CSS;
use simplerest\core\libs\XML;
use simplerest\core\libs\Files;

class Angular
{
    /*
        Elimina vestigios de Angular / AngularJS
        de un HTML
    */
    static function remove($page)
    {
        if (file_exists($page)){
            $page = Files::getContent($page);
        }   

        $page = XML::removeHTMLAttributes($page, [
            'data-ng-click',
            'data-ng-if',
            'data-ng-class',
            'data-ng-repeat',
            'data-ng-model',
            'data-ng-show',
            'data-ng-options',
            'data-ng-maxlength',
            'data-ng-messages',
            'data-ng-bind',
            'data-ng-disabled',
            'data-ng-value',
            'lang-bind',
            'data-toggle',
            'lang-bind-attr'
        ]);

        // $page = str_replace([
        //     '-maxlength',
        //     '-required',
        //     '-img',
        //     '-item',
        //     '-secondary'
        // ], '', $page);

        $page = str_replace('ng-hide', 'd-none', $page);

        $page = CSS::removeCSSClasses($page,[
            'ng-pristine',
            'ng-untouched',
            'ng-valid',
            'ng-not-empty',
            'ng-inactive'
        ]);

        $page = XML::removeEmptyAttributes($page);

        return $page;
    }


}


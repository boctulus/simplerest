<?php

namespace Boctulus\Simplerest\Core\Libs\code_cleaner;

use Boctulus\Simplerest\Core\Libs\CSS;
use Boctulus\Simplerest\Core\Libs\HTML;
use Boctulus\Simplerest\Core\Libs\Files;

class AngularCleaner
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

        $page = HTML::removeHTMLAttributes($page, [
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

        $page = str_replace('ng-hide', 'd-none', $page);

        $page = CSS::removeCSSClasses($page,[
            'ng-pristine',
            'ng-untouched',
            'ng-valid',
            'ng-not-empty',
            'ng-inactive'          
        ]);

        $page = HTML::removeEmptyAttributes($page);

        return $page;
    }

}


<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;
use simplerest\core\libs\i18n\Translate;

class TranslatorController extends Controller
{
       /*
        Ver mejores soluciones como:

        https://github.com/php-gettext/Gettext
        https://github.com/pherrymason/PHP-po-parser

        Más
        https://stackoverflow.com/a/16744070/980631
    */
    function test_export_lang()
    {
        Translate::exportLangDef();
    }

    function test_trans()
    {
        //Translate::useGettext(true); // usar funciones nativas

        setLang('es');

        // i18n
        Translate::bind('validator');

        // El campo es requerido (traducido)
        dd(
            trans('field is required'), 
            Translate::getLocale()
        );
    }

    function test_trans_2()
    {
        //Translate::useGettext(true);

        setLang('es');

        dd(Translate::getDomain(), 'Current Text Domain');

        dd(
            trans('field is required', 'validator'), 
            Translate::getLocale()
        );

        // Translate::bind('validator');

        // dd(
        //     trans('field is required'), 
        //     Translate::getLocale()
        // );
    }

    // OK
    function test_po_parser(){
        //Translate::useGettext(false); // usar alternativa
        
        Translate::bind('validator');
        
        dd(
            trans("It's not a valid float")
        );
    }

}


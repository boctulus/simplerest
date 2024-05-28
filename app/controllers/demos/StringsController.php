<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\Strings;
use simplerest\core\controllers\Controller;

/*
    Ajustar nomenclatura a esta misma test_{nombre de metodo snake-case}
    y... convertir en pruebas unitarias
*/
class StringsController extends Controller
{
    // ok
    function test_to_snake_case(){
        return Strings::toSnakeCase('MAYORISTA CSV Import');
    }

    // getUpToNWords
    function test_get_up_to_n_words(){
        return Strings::getUpToNWords('El dia que me quieras', 3);
    }

    // ok
    function test_get_up_to() {
        dd(Strings::getUpTo("MAYORISTA CSV Impor", PHP_INT_MAX, 15)); // MAYORISTA CSV
        dd(Strings::getUpTo("MAYORISTA CSV Impor", null, 15)); // MAYORISTA CSV I

        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo", 4, 10));
        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo", 4, 17));
        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo", 4, 18));
        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo", 4, 19));
        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo", 3));
        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo", null, 12));
        // dd(Strings::getUpTo("Esta es una prueba de método getUpTo"));
    }

    // ...
}


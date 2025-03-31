<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;

abstract class Reviews
{
    static protected $lang    = null;
    static protected $country = null;

     /**
     * @param string|null $subject
     * @return string
     * @throws \Exception
     */
    static function getParaphrase($subject = null){
        throw new \Exception("Not implemented");
    }

    /*
        Devuelve un nombre falso en el idioma seleccionado

        Podria usar Faker?
    */
    static function getFullName($gender = null, $lang = null, $country = null) 
    {
        $lang    = $lang    ?? static::$lang;
        $country = $country ?? static::$country;

        if ($gender == 'male'){
            $gender == 'm';
        }

        if ($gender == 'female'){
            $gender == 'f';
        }

        if ($gender == 'neutro'){
            $gender == 'n';
        }

        if ($gender == 'n'){
            $gender = (rand(0,1) == 1) ? 'm' : 'f';
        }

        // Obtener un nombre al azar
        $random_name = table('common_names')
        ->when(!empty($lang), function($q) use ($lang){
            $q->where([
                'language' => $lang
            ]);
        })
        ->when(!empty($country), function($q) use ($country){
            $q->where([
                'country' => $country
            ]);
        })
        ->when(!empty($gender), function($q) use ($gender){
            $q->where([
                'gender' => $gender
            ]);
        })
        ->random()
        ->value('text');

        // Obtener un apellido al azar
        $random_surname = table('common_surnames')->random()->value('text');
    
        // Generar un número aleatorio entre 1 y 100 para determinar si habrá segundo nombre o inicial
        $random_percent = rand(1, 100);
    
        // Inicializar $second_name
        $second_name = '';
    
        // 66% de las veces no hay segundo nombre ni inicial
        if ($random_percent > 66) {
            // 11% de las veces hay inicial
            if ($random_percent <= 77) {
                $second_name = $random_name[0] . '.';
            } 
            // 22% de las veces hay segundo nombre
            else {
                $second_name = table('common_names')->random()->value('text');
            }
        }
    
        // Formar y devolver el nombre completo
        $full_name = $random_name;
        if (!empty($second_name)) {
            $full_name .= ' ' . $second_name;
        }
        $full_name .= ' ' . $random_surname;
    
        return $full_name;
    }

    // ..
}


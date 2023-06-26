<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class LangDetector
{
    protected $common_words = [
        'en' => [
            'in', 'on', 'at', 'to', 'for', 'by', 'with', 'from', 'about', 'against', 'between',
            'through', 'during', 'before', 'after', 'above', 'below', 'over', 'under', 'down', 
            'up', 'with', 'of', 'and', 'best', 'only', 'are', 'will', 'able', 'have', 'had', 'get',
            'that', 'most', 'was', 'were', 'which', 'each', 'more', 'less', 'make', 'made', 'does', 
            'like', 'your', 'been', 'all', 'into', 'take', 'since', 'buy', 'sell', 'solution'
        ],
        'es' => [
            'bajo', 'con', 'contra', 'desde', 'hacia', 'hasta', 'para', 'mediante', 'una', 'este', 
            'del', 'esta', 'como', 'menos', 'cierto', 'gratis', 'más', 'fácil', 'crear', 'cantidad', 'solo',
            'muy', 'mucho', 'mucha', 'muchos', 'muchas', 'tipo', 'cualquier', 'cualquiera', 'poder', 'hacer',
            'crecer', 'gran', 'sitio', 'puede', 'permite', 'permitir', 'tienda', 'comercio' 
        ]
    ];

    protected $common_word_groups = [
        'en' => [
            'is an', 'is a'
        ],
        'es' => [
            'es un', 'es una', 'son las', 'es la', 'por lo tanto', 'por medio', 'en la', 'en el',
            'de la', 'de los', 'de las'
        ]
    ];

    static function is(string $str, string $lang){
       
    } 


}


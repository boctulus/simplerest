<?php

namespace Boctulus\Simplerest\Core\Libs;

/*
    @author Pablo Bozzolo < boctulus@gmail.com >

    Detector de lenguaje efectivo y sencillo

    Lenguas soportadas: ingles, castellano, italiano, hindi y bengali

    Puede extenderse la clase para disernir entre mas idiomas
    o agregar nuevas palabras
*/

use Boctulus\Simplerest\Core\Libs\Strings;

class LangDetector
{
    protected static $common_words = [
        'en' => [
            'in', 'on', 'at', 'to', 'for', 'by', 'with', 'from', 'about', 'against', 'between',
            'through', 'during', 'before', 'after', 'above', 'below', 'over', 'under', 'down', 
            'up', 'with', 'of', 'and', 'or', 'best', 'only', 'is', 'are', 'will', 'able', 'have', 'had', 
            'get', 'that', 'most', 'was', 'were', 'each', 'more', 'less', 'make', 'made', 
            'does', 'like', 'your', 'been', 'all', 'going', 'into', 'take', 'since', 'buy', 'sell', 
            'but', 'this', 'that', 'free', 'type', 'allow', 'allows', 'solution', 'add', 'adds',
            'which', 'where', 'who', 'how', 'if',
            'big', 'small', 'very', 'give', 'gives'
        ],

        'es' => [
            'bajo', 'con', 'contra', 'desde', 'hacia', 'hasta', 'para', 'mediante', 'una', 'este', 
            'del', 'esta', 'como', 'menos', 'cierto', 'gratis', 'más', 'fácil', 'crear', 'cantidad', 'solo',
            'muy', 'mucho', 'mucha', 'muchos', 'muchas', 'tipo', 'cualquier', 'cualquiera', 'poder', 'hacer',
            'casi', 'poco', 'crecer', 'gran', 'sitio', 'puede', 'permite', 'permitir', 'y', 'o', 'de', 'del',
            'que', 'cual', 'quien', 'donde', 'cuando', 'si', 'su', 'sus'
        ],

        'it' => [
            'è', 'più', 'meno', 'di', 'da', 'per', 'tra', 'fra', 'su', 'sopra', 'sotto', 'sulla', 'sulle',
            'sul', 'sui', 'dalla', 'dalle', 'dallo', 'dai', 'dagli', 'serve', 'prima', 'siamo', 'qualcuno',
            'qualcosa', 'questo', 'quello', 'quella', 'quelli', 'quelle', 'questi', 'queste', 'che', 'chi',
            'dove', 'quando', 'se', 'suo', 'sua', 'suoi', 'sue'
        ]
    ];

    protected static $common_word_groups = [
        'en' => [
            'is an', 'is a', 'are the', 'middle of', 'kind of', 'type of', 'for free', 'for any', 'is the', 
            'are the', 'it is', "it's"
        ],

        'es' => [
            'es un', 'es una', 'son las', 'es la', 'por lo tanto', 'por medio', 'en la', 'en el',
            'de la', 'de los', 'de las', 'con una', 'con unas', 'que le', 'que les', 'tipo de', 'ya que'
        ],

        'it' => [
            'è un', 'è una', 'sono le', 'è la', 'tipo di', 'perché'
        ]
    ];

    protected static $unicode_scripts = [
        'hi' => '/[\x{0900}-\x{097F}]/u',
        'bn' => '/[\x{0980}-\x{09FF}]/u',
    ];

    static function langs(){
        return array_values(array_unique(array_merge(
            array_keys(static::$common_words),
            array_keys(static::$unicode_scripts)
        )));
    }

    /*
        Retorna un score de 0 a 100 con una "probabilidad" estimada
        de que el texto este escrito en cierto idioma

        Luego se puede escribir una regla de desicion adaptada al contexto

        Ej:

        $en_score = LangDetector::is($str, 'en');
        $es_score = LangDetector::is($str, 'es');

        if ($es_score == 0 && $en_score == 0){
            $lang = '??';
        } else {
            if ($es_score >= $en_score){
                $lang = 'es';
            } elseif ($en_score > $es_score) {
                $lang = 'en';
            }
        }          
    */
    static function is(string $str, string $lang){
        if (!in_array($lang, static::langs())){
            throw new \InvalidArgumentException("Language is not supported");
        }

        if (isset(static::$unicode_scripts[$lang])){
            return preg_match(static::$unicode_scripts[$lang], $str) === 1 ? 100 : 0;
        }

        $score = 0;
        foreach(static::$common_word_groups[$lang] ?? [] as $word){
            if (Strings::contains($word, $str, false)){
                $score += 49;
            }
        }

        $word_count = Strings::wordsCount($str);

        if ($word_count === 0){
            return 0;
        }

        $word_score = 0;
        foreach(static::$common_words[$lang] as $word){
            if (Strings::containsWord($word, $str, false)){
                $word_score += 1;
            }
        }

        $score += round(100 * ($word_score / $word_count));
        
        return min($score, 100);
    } 

    public static function detect(string $str): ?string
    {
        $scores = [];
        foreach (static::langs() as $lang){
            $scores[$lang] = static::is($str, $lang);
        }

        arsort($scores);
        $bestLang = array_key_first($scores);
        $bestScore = $scores[$bestLang] ?? 0;
        $topScores = array_values($scores);

        if ($bestScore <= 0 || (($topScores[1] ?? 0) === $bestScore)){
            return null;
        }

        return $bestLang;
    }


}


<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;
use simplerest\libs\GrammarAnalyzer;

class ItalianGrammarAnalyzer extends GrammarAnalyzer
{
    /*
        Retorna genero: 'f', 'm' o 'n'
    */
    static function getGender($sentence) {
        // Convertir a minúsculas para ignorar el caso
        $s = strtolower($sentence);
    
        // Protejo la oracion de "molto" ya que termina en "-to" a pesar de ser adverbio.
        $s = preg_replace('/sono\s+molto/', 'sono bene', $s);

        if (Strings::containsAnyWord(['marito', 'fidanzato'], $s)){
            return 'f';
        }

        if (Strings::containsAnyWord(['moglie', 'fidanzata'], $s)){
            return 'm';
        }

        $f_patt = [
            '/sono\s+(\w*issima)/',
            '/sono\s+(\w*ta)/',
            '/sono\s+\w+\s+(\w*ta)/',
        ];

        $m_patt = [
            '/sono\s+(\w*issimo)/',
            '/sono\s+(\w*to)/',
            '/sono\s+\w+\s+(\w*to)/'
        ];

        // dd($s, 'Sentence'); 

        foreach ($f_patt as $p){
            if (Strings::match($s, $p)){
                return 'f';
            }
        }

        foreach ($m_patt as $p){
            if (Strings::match($s, $p)){
                return 'm';
            }
        }
       
       return 'n';
    }

}


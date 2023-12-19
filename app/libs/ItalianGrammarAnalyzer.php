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

        if (Strings::containsAnyWord(['moglie', 'fidanzata'], $s)){
            return 'm';
        }

        if (Strings::containsAnyWord(['marito', 'fidanzato'], $s)){
            return 'f';
        }

        $m_patt = [
            '/sono\s+(\w*issimo)/',
            '/sono\s+(\w*to)/',
            '/sono\s+\w+\s+(\w*to)/',
            '/sono\s+(\w*ico)/',
            '/sono\s+\w+\s+(\w*ico)/'            
        ];

        $f_patt = [
            '/sono\s+(\w*issima)/',
            '/sono\s+(\w*ta)/',
            '/sono\s+\w+\s+(\w*ta)/',
            '/sono\s+(\w*ica)/',
            '/sono\s+\w+\s+(\w*ica)/',
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

        if (Strings::containsAnyWord(['da donna'], $s)){
            return 'f';
        }       
        
        if (Strings::containsAnyWord(['da uomo'], $s)){
            return 'm';
        }   
       
       return 'n';
    }

}


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

        Strings::replace('perfetto', 'perfect', $s);
        Strings::replace('perfetta', 'perfect', $s);

        if (Strings::containsAnyWord(['moglie', 'fidanzata'], $s)){
            return 'm';
        }

        if (Strings::containsAnyWord(['marito', 'fidanzato'], $s)){
            return 'f';
        }

        $m_patt = [
            '/sono\s+(\w*issimo)/',
            '/sono\s+(\w*[oò]to)\b/u',
            '/sono\s+\w+\s+(\w*[oò]to)\b/u',
            '/sono\s+(\w*ico)/',
            '/sono\s+\w+\s+(\w*ico)/',

            # caso ", soddisfatto"

            '/,\s+(\w*tto)/',
            
            # caso ", molto soddisfatto"
      
            '/,\s+(molto)?\s*(\w*tto)/',
            '/,\s+(davvero)?\s*(\w*tto)/',
        ];

        $f_patt = [
            '/sono\s+(\w*issima)/',
            '/sono\s+(\w*[aà]ta)\b/u',
            '/sono\s+\w+\s+(\w*[aà]ta)\b/u',
            '/sono\s+(\w*ica)/',
            '/sono\s+\w+\s+(\w*ica)/',

            # caso ", soddisfatta"

            '/,\s+(\w*tta)/',

            # caso ", molto soddisfatta"

            '/,\s+(molto)?\s*(\w*tta)/',
            '/,\s+(davvero)?\s*(\w*tta)/',
        ];

        // dd($s, 'Sentence'); 

        foreach ($m_patt as $p){
            if (Strings::match($s, $p)){
                return 'm';
            }
        }

        foreach ($f_patt as $p){
            if (Strings::match($s, $p)){
                return 'f';
            }
        }

        if (Strings::containsAnyWord(['da uomo'], $s)){
            return 'm';
        }   

        if (Strings::containsAnyWord(['da donna', 'vestito', 'gonna'], $s)){
            return 'f';
        }       
        

       return 'n';
    }

}


<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class ItalianReviews extends Reviews
{
    static protected $lang    = 'it';
    static protected $country = null;

    /*
        Dada una frase de un review en *italiano*, le agrega variabiildad extra 
    */
    static function randomizePhrase(string $text){
        $text = Strings::replaceSubstringRandomly($text, 'Ho trovato', [
            'In questo sito ho trovato',
            'Recentemente ho scoperto',
            'Navigando online ho individuato',
            'Mi sono imbattuto in',
            'Ho fatto una scoperta su',
            'Ho incontrato',
            'Ho ottenuto',
            'Ho selezionato',
            // Agrega más reemplazos según sea necesario
        ]);

        $text = Strings::replaceSubstringRandomly($text, 'Ho acquistato', [
            'In questo sito ho trovato',
            'Recentemente ho scoperto',
            'Navigando online ho individuato',
            'Mi sono imbattuto in',
            'Ho fatto una scoperta su',
            'Ho incontrato',
            'Ho ottenuto',
            'Ho selezionato',
            // Agrega más reemplazos según sea necesario
        ]);

        $text = Strings::replaceSubstringRandomly($text, 'Ho incontrato', [
            'In questo sito ho incontrato',
            'Ho fatto la conoscenza di',
            'Ho avuto l\'opportunità di conoscere',
            'Mi sono imbattuto in',
            'Ho fatto la mia conoscenza con',
            'Ho incontrato casualmente',
            'Ho avuto un incontro con',
            // Agrega más reemplazos según sea necesario
        ]);
        
        $text = Strings::replaceSubstringRandomly($text, 'Ho ordinato', [
            'La settimana scorsa ho ordinato',
            'Alcuni giorni fa ho effettuato l\'ordine',
            'Ho fatto un ordine la scorsa settimana',
            'Qualche giorno fa ho effettuato una richiesta d\'acquisto',
            'Ho ordinato online',
            'La settimana passata ho fatto un ordine',
            'Ho effettuato una prenotazione',
            // Agrega más reemplazos según sea necesario
        ]);            
        
        $text = Strings::replaceSubstringRandomly($text, 'appena', [
            'appena adesso',
            'proprio ora',
            'ora stesso',
            'da poco',
            'recentemente'
        ]);

        $text = Strings::replaceSubstringRandomly($text, 'vivamente', [
            'entusiasticamente',
            'con fervore',
            'con grande passione',
            'con vivo interesse',
            'con energia',
            // Agrega más reemplazos según sea necesario
        ]);

        
        $pre = '';
        if (rand(0,10) >= 7){
            $pre = static::getParaphrase(). ' ';
        }

        return ucfirst("{$pre}$text"); 
    }

    /*
        Maneja 3 tipos arrays, uno para frases (donde el hablante es) de genero neutro como "Il mio acquisto è stato un vero piacere con {subject}"
        o "Il mio acquisto è stato un vero piacere.", y tambien para genero masculino y femenino
    */
    static function getParaphrase($gender = 'n', $subject = null){
        static $prev_index;

        if (!empty($subject)){
            $frasiGenerali = [
                'm' => [                   
                    "Sono molto contento di {subject}",                  
                    "Sono emozionato di condividere la mia esperienza con {subject}",
                    "Mi sento molto fortunato ad aver scelto {subject}",
                    "Sono euforico per {subject}",
                    "Sono molto contento della mia scelta di {subject}",
                ],
                'f' => [
                    "Sono molto contenta di {subject}",                  
                    "Sono emozionata di condividere la mia esperienza con {subject}",
                    "Mi sento molto fortunata ad aver scelto {subject}",
                    "Sono euforica per {subject}",
                    "Sono molto contenta della mia scelta di {subject}",
                ],
                'n' => [
                    "Sono molto soddisfatto di {subject}",
                    "La mia esperienza è stata eccellente con {subject}",
                    "Non potrei essere più felice con {subject}",
                    "Voglio esprimere la mia gioia per {subject}",
                    "Sono entusiasta di condividere la mia opinione su {subject}",
                    "Lascio la mia recensione con gioia su {subject}",
                    "Sono felice di dire che {subject}",
                    "Con tutta la felicità del mondo, voglio dire che {subject} è ottimo",                    
                    "Non posso nascondere la mia allegria per {subject}",                   
                    "Voglio esprimere la mia completa soddisfazione con {subject}",
                    "Mi sento molto fortunato ad aver scelto {subject}",
                    "Sono pieno di gioia con {subject}",
                    "Lascio la mia opinione con entusiasmo su {subject}",
                    "Sono estremamente felice con {subject}",
                    "Voglio sottolineare quanto sono felice con {subject}",
                    "Il mio acquisto ha superato le mie aspettative con {subject}",
                    "Sono radiante di felicità con {subject}",
                    "Non posso evitare di sorridere parlando di {subject}",
                    "Il mio cuore trabocca di gioia al pensiero di {subject}",
                    "Sono completamente felice con {subject}",
                    "Lascio la mia recensione con la massima soddisfazione su {subject}",                    
                    "Voglio condividere la mia felicità menzionando {subject}",
                    "Sono davvero entusiasta di raccontare la mia esperienza con {subject}",
                    "Non ci sono parole per descrivere la mia felicità con {subject}",
                    "Sono grato e felice di aver scelto {subject}",
                    "Lascio la mia testimonianza con molta gioia su {subject}",
                    "Non posso esprimere quanto sono felice con {subject}",
                    "Sono pieno di gratitudine per {subject}",
                    "Voglio manifestare la mia gioia per {subject}",                    
                    "Non posso smettere di sorridere pensando a {subject}",
                    "La mia soddisfazione è completa con {subject}",
                    "Voglio condividere il mio entusiasmo per {subject}",                    
                    "Il mio acquisto è stato un vero piacere con {subject}",
                ]
            ];

            $frasiGenerali['m'] = array_merge($frasiGenerali['m'], $frasiGenerali['n']);
            $frasiGenerali['f'] = array_merge($frasiGenerali['f'], $frasiGenerali['n']);

            /*
                Evito repetir
            */
    
            $index = array_rand($frasiGenerali[$gender]);

            if (isset($prev_index)){
                while ($index == $prev_index){
                    $index = array_rand($frasiGenerali[$gender]);
                }
            }
    
            $prev_index = $index;

            return str_replace('{subject}', $subject, $frasiGenerali[$gender][$index]);
        }
            
        // else
    
        $frasiGenerali = [
            'm' => [
                "Sono molto contento.",
                "Sono emozionato di condividere la mia esperienza.",
                "Mi sento molto fortunato ad aver scelto questo.",
                "Sono euforico.",
                "Sono molto contento della mia scelta.",
            ],
            'f' => [
                "Sono molto contenta.",
                "Sono emozionata di condividere la mia esperienza.",
                "Mi sento molto fortunata ad aver scelto questa.",
                "Sono euforica.",
                "Sono molto contenta della mia scelta.",
            ],
            'n' => [     
                "La mia esperienza è stata eccellente.",
                "Non potrei essere più felice.",
                "Voglio esprimere la mia gioia.",
                "Sono entusiasta di condividere la mia opinione.",
                "Lascio la mia recensione con gioia.",
                "Sono felice di dire che è ottimo.",
                "Con tutta la felicità del mondo, voglio dire che è ottimo.",
                "Non posso nascondere la mia allegria.",
                "Voglio esprimere la mia completa soddisfazione.",                
                "Sono pieno di gioia.",
                "Lascio la mia opinione con entusiasmo.",
                "Sono estremamente felice.",
                "Voglio sottolineare quanto sono felice.",
                "Il mio acquisto ha superato le mie aspettative.",
                "Sono radiante di felicità.",
                "Non posso evitare di sorridere parlando di questo.",
                "Il mio cuore trabocca di gioia al pensiero di questo.",
                "Sono completamente felice.",
                "Lascio la mia recensione con la massima soddisfazione.",
                "Voglio condividere la mia felicità menzionando questo.",
                "Sono davvero entusiasta di raccontare la mia esperienza.",
                "Non ci sono parole per descrivere la mia felicità.",
                "Sono grato e felice di aver scelto questo.",
                "Lascio la mia testimonianza con molta gioia.",
                "Non posso esprimere quanto sono felice.",
                "Sono pieno di gratitudine.",
                "Voglio manifestare la mia gioia.",                
                "Non posso smettere di sorridere pensando a questo.",
                "La mia soddisfazione è completa.",
                "Voglio condividere il mio entusiasmo.",
                "Il mio acquisto è stato un vero piacere.",
                "Il mio livello di soddisfazione è al massimo.",
                "L'esperienza è stata al di sopra di ogni aspettativa.",
            ]
        ];

        $frasiGenerali['m'] = array_merge($frasiGenerali['m'], $frasiGenerali['n']);
        $frasiGenerali['f'] = array_merge($frasiGenerali['f'], $frasiGenerali['n']);
        
        /*
            Evito repetir
        */

        $index = array_rand($frasiGenerali[$gender]);

        if (isset($prev_index)){
            while ($index == $prev_index){
                $index = array_rand($frasiGenerali[$gender]);
            }
        }

        $prev_index = $index;

        return $frasiGenerali[$gender][$index];     
    }

}


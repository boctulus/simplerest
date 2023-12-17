<?php

namespace simplerest\controllers\review_generator;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;
use simplerest\shortcodes\star_rating\StarRatingShortcode;

class ReviewsController extends MyController
{
    function rating_random(){
        // Generar 21 registros con datos al azar
        for ($i = 0; $i < 21; $i++) {
            $comment = "Comentario #" . ($i + 1);
            $score = rand(1, 5);
            $clientName = "Cliente " . chr(rand(65, 90)) . rand(1, 99); // Cliente A, Cliente B, etc.
            $createdAt = now(); // Fecha y hora actual

            // Construir la consulta INSERT
            $insertQuery = "INSERT INTO star_rating (comment, score, author, created_at) VALUES ('$comment', $score, '$clientName', '$createdAt');";

            // Ejecutar la consulta
            DB::statement($insertQuery);
        }

        dd("Registros insertados exitosamente.");
    }

    /*
        Test de shortcode
    */
    function rating_slider(){        
        $sc = new StarRatingShortcode();

        render($sc->rating_slider());
    }

    /*
        Test de shortcode
    */
    function rating_table()
    {
        $sc = new StarRatingShortcode();

        render($sc->rating_table());
    }

    function insert_names(){
        $names    = include ETC_PATH . 'review-generator/common_names-it.php'; // array
        $surnames = include ETC_PATH . 'review-generator/common_surnames-it.php'; // array

        $now = at();
        foreach ($names as $name){
            DB::insert("INSERT IGNORE INTO `common_names` (`id`, `text`, `language`, `country`, `created_at`) VALUES (NULL, '$name', 'it', NULL, '$now');");
        }

        foreach ($surnames as $name){
            DB::insert("INSERT IGNORE INTO `common_surnames` (`id`, `text`, `language`, `country`, `created_at`) VALUES (NULL, \"$name\", 'it', NULL, '$now');");
        }
    }

    function get_full_it_name() {
        // Obtener un nombre al azar
        $random_name    = table('common_names')->random()->value('text');
    
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

    function parse_answers(){
        $path = 'D:\www\simplerest\etc\review-generator\answers.txt';
        $file = file_get_contents($path);

        $rows = Strings::lines($file, true, true);

        // ahora debo seguir limpiando... eliminar duplicados e insertar
        foreach ($rows as $ix => $row){
            $ok = preg_match_all('/([a-zA-Z].*)/', $row, $matches);
            
            if ($ok){
                $rows[$ix] = rtrim($matches[0][0], '"');
            }            
        }

        $rows = array_unique($rows);

        DB::getConnection();

        $prefix = config()['tb_prefix'];

        foreach($rows as $row) {
            $comment = $row;
            $score = rand(4, 5);
            $clientName = $this->get_full_it_name();
            $createdAt = now(); // Fecha y hora actual

            // Construir la consulta INSERT
            $insertQuery = "INSERT INTO {$prefix}star_rating (comment, score, author, created_at) VALUES (\"$comment\", $score, \"$clientName\", '$createdAt');";

            dd($insertQuery);

            // Ejecutar la consulta
            DB::statement($insertQuery);    
        }

        dd("Registros insertados exitosamente.");
    }

    # php com review_generator reviews test_name_gen
    function test_name_gen(){
        for ($i=0; $i<500; $i++){
            print_r(
                $this->get_full_it_name() . PHP_EOL
            );
        }
    }

    function get_paraphrase($subject = null){
        if (!empty($subject)){
            $frasiGenerali = [
                "Sono molto soddisfatto di {subject}",
                "La mia esperienza è stata eccellente con {subject}",
                "Non potrei essere più felice con {subject}",
                "Voglio esprimere la mia gioia per {subject}",
                "Sono entusiasta di condividere la mia opinione su {subject}",
                "Lascio la mia recensione con gioia su {subject}",
                "Sono felice di dire che {subject}",
                "Con tutta la felicità del mondo, voglio dire che {subject} è ottimo",
                "Sono molto contento di {subject}",
                "Non posso nascondere la mia allegria per {subject}",
                "Sono emozionato di condividere la mia esperienza con {subject}",
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
                "Sono euforico per {subject}",
                "Voglio condividere la mia felicità menzionando {subject}",
                "Sono davvero entusiasta di raccontare la mia esperienza con {subject}",
                "Non ci sono parole per descrivere la mia felicità con {subject}",
                "Sono grato e felice di aver scelto {subject}",
                "Lascio la mia testimonianza con molta gioia su {subject}",
                "Non posso esprimere quanto sono felice con {subject}",
                "Sono pieno di gratitudine per {subject}",
                "Voglio manifestare la mia gioia per {subject}",
                "Sono molto contento della mia scelta di {subject}",
                "Non posso smettere di sorridere pensando a {subject}",
                "La mia soddisfazione è completa con {subject}",
                "Voglio condividere il mio entusiasmo per {subject}",
                "Sono davvero emozionato di condividere la mia opinione su {subject}",
                "Il mio acquisto è stato un vero piacere con {subject}",
            ];
    
            return str_replace('{subject}', $subject, $frasiGenerali[array_rand($frasiGenerali)]);
        }
            
        // else

        $frasiGenerali = [
            "Sono molto soddisfatto del mio acquisto.",
            "La mia esperienza è stata eccezionale.",
            "Non potrei essere più felice.",
            "Voglio esprimere la mia gioia.",
            "Sono entusiasta di condividere la mia opinione.",
            "Lascio la mia recensione con gioia.",
            "Sono felice di dirvi che sono completamente soddisfatto.",
            "Con tutta la felicità del mondo, voglio dire che sono estremamente contento.",
            "Sono molto contento dell'acquisto.",
            "Non posso nascondere la mia allegria.",
            "Sono emozionato di condividere la mia esperienza.",
            "Voglio esprimere la mia completa soddisfazione.",
            "Mi sento molto fortunato per la scelta fatta.",
            "Sono pieno di gioia.",
            "Lascio la mia opinione con entusiasmo.",
            "Sono estremamente felice.",
            "Voglio sottolineare quanto sia felice.",
            "Il mio acquisto ha superato le mie aspettative.",
            "Sono radiante di felicità.",
            "Non posso evitare di sorridere pensando a quanto sono soddisfatto.",
            "Il mio cuore trabocca di gioia al pensiero di questa esperienza.",
            "Sono completamente felice.",
            "Lascio la mia recensione con la massima soddisfazione.",
            "Sono euforico.",
            "Voglio condividere la mia felicità con voi.",
            "Sono davvero entusiasta di raccontare la mia esperienza.",
            "Non ci sono parole per descrivere quanto sono felice.",
            "Sono grato e felice della scelta fatta.",
            "Lascio la mia testimonianza con molta gioia.",
            "Non posso esprimere quanto sia felice.",
            "Sono pieno di gratitudine.",
            "Voglio manifestare la mia gioia.",
            "Sono molto contento della mia scelta.",
            "Non posso smettere di sorridere pensando a quanto sono soddisfatto.",
            "La mia soddisfazione è completa.",
            "Voglio condividere il mio entusiasmo.",
            "Sono davvero emozionato di condividere la mia opinione.",
            "Il mio acquisto è stato un vero piacere."
        ];        
        
        return $frasiGenerali[array_rand($frasiGenerali)];     
    }

    function get_reviews(){
        $reviews = table('star_rating')->get();

        foreach ($reviews as $ix => $review){
            $text = $review['comment'];

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

            $pre = '';
            if (rand(0,10) >= 7){
                $pre = $this->get_paraphrase(). ' ';
            }

            $reviews[$ix]['text'] = "{$pre}$text"; 
            
            dd($reviews[$ix]['text'], null, false);
        }
    }

}


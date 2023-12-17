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

    function get_reviews(){
        $reviews = table('star_rating')->get();

        foreach ($reviews as $ix => $review){
            $text = $review['comment'];
            
            $text = Strings::replaceSubstringRandomly($text, 'appena', [
                'appena adesso',
                'proprio ora',
                'ora stesso',
                'da poco',
                'recentemente'
            ]);

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
            
            $reviews[$ix]['text'] = $text; 
            
            dd($text, null, false);
        }
    }

}


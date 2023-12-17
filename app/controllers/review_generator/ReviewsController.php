<?php

namespace simplerest\controllers\review_generator;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\libs\ItalianReviews;
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

    function parse_answers(){
        $path = 'D:\www\simplerest\etc\review-generator\answers.txt';
        $file = file_get_contents($path);

        $rows = Strings::lines($file, true, true);

        // dd(count($rows));
        // exit;

        // ahora debo seguir limpiando... eliminar duplicados e insertar
        foreach ($rows as $ix => $row){
            $ok = preg_match_all('/([a-zA-Z].*)/', $row, $matches);
            
            if ($ok){
                $rows[$ix] = rtrim($matches[0][0], '"');
            }  else {
                dd($row, "ERROR para ...");
                exit;
            }          
        }

        $rows = array_unique($rows);

        // dd(count($rows));
        // exit;

        return $rows;     
    }

    function insert_answers()
    {
        $prefix = config()['tb_prefix'];

        DB::truncate("{$prefix}star_rating"); ///    

        $rows = $this->parse_answers();

        foreach($rows as $row) {
            $comment    = $row;
            $score      = rand(4, 5);
            $clientName = ItalianReviews::getFullName();
            $createdAt  = now(); 

            // <---------------------------------------------- aca debo meter la variabilidad
            $comment    = ItalianReviews::randomizePhrase($comment);

            // Construir la consulta INSERT
            $insertQuery = "INSERT INTO {$prefix}star_rating (comment, score, author, created_at) VALUES (\"$comment\", $score, \"$clientName\", '$createdAt');";

            dd($insertQuery);

            // Ejecutar la consulta
            DB::statement($insertQuery);    
        }

        dd("Registros insertados exitosamente.");
    }
    
    function test()
    {
        $rows = $this->parse_answers();

        foreach($rows as $row) {
            $comment    = $row;
            $comment    = ItalianReviews::randomizePhrase($comment);

            dd($comment);
        }
    }

    # php com review_generator reviews test_name_gen
    function test_name_gen(){
        for ($i=0; $i<500; $i++){
            print_r(
                ItalianReviews::getFullName() . PHP_EOL
            );
        }
    }

    function get_reviews(){
        $reviews = table('star_rating')->get();

        foreach ($reviews as $ix => $review){
            $text = $review['comment'];

            $reviews[$ix]['text'] = ItalianReviews::randomizePhrase($text); 
            
            dd($reviews[$ix]['text']);
        }
    }

}


<?php

namespace simplerest\controllers\review_generator;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\libs\ItalianReviews;
use simplerest\controllers\MyController;
use simplerest\libs\ItalianGrammarAnalyzer;
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

    function parse(){
        $path = 'D:\www\simplerest\etc\review-generator\answers.php';
        $rows = include $path;

        foreach ($rows as $ix => $row){
            // Elimina lo que está entre la última "!" o "." y "]"
            $rows[$ix] = preg_replace('/[!.\[]([^!.\[]*?)\]/', '', $rows[$ix]);

            // Elimina números seguidos de punto al inicio
            $rows[$ix] = preg_replace('/^\d+\./', '', $rows[$ix]);

            // Agrega un punto al final si no termina en "!" o "."
            if (!preg_match('/[!.\]]$/', $rows[$ix])) {
                $rows[$ix] .= '.';
            }
        }

        $rows = array_unique($rows);

        // dd(count($rows));
        // exit;

        foreach($rows as $row){
            $gender = ItalianGrammarAnalyzer::getGender($row);
            dd($row . "[$gender]", null, false);
        }

        return $rows;     
    }

    function insert_answers()
    {
        $prefix = config()['tb_prefix'];

        $rows = $this->parse();

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
        $rows = $this->parse();

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


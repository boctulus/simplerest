<?php

namespace Boctulus\Simplerest\Controllers\review_generator;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\ApiClient;
use Boctulus\Simplerest\Core\Libs\ChatGPT;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\RandomGenerator;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Libs\ItalianGrammarAnalyzer;
use Boctulus\Simplerest\Libs\ItalianReviews;
use Boctulus\Simplerest\Modules\StarRating\StarRating;


class ReviewsController extends Controller
{
    /*
        Falta completar parseando la salida
        e insertando

        Revisar parse() e insert_answers()
    */
    function generate_using_chatgpt($qty = 3) {
        $prompt = "Write $qty positive reviews in Italian for E-commerce that sells clothes, accessories such as bags and shoes for men, women and children";
        $sys    = "Format the output as a Python unidimensional array. Avoid extra comments";

        $prompt = "$prompt. $sys"; 

        /*
            Intento estimar cantidad de tokens
        */
        $max_tokens = 50 + (30 * $qty);

        if ($max_tokens > (2048 - 50)){
            die("Demasiados tokens. Re-preguntar"); 
        }

        $chat = new ChatGPT();

        $chat->setParams([
            "max_tokens"      => $max_tokens,
            "temperature"     => 0.9
        ]);

        $chat->addContent($prompt);
        $res = $chat->exec();  

        dd($res);
    }
    
    
    /*
        Test de shortcode
    */
    function rating_slider(){        
        $sc = new StarRating();

        render($sc->rating_slider());
    }

    /*
        Test de shortcode
    */
    function rating_table()
    {
        $sc = new StarRating();

        render($sc->rating_table());
    }

    function insert_names(){
        $names_male   = include ETC_PATH . 'py-review-generator/common_names_male-it.php'; // array
        $names_female = include ETC_PATH . 'py-review-generator/common_names_female-it.php'; 
        $surnames     = include ETC_PATH . 'py-review-generator/common_surnames-it.php'; // array

        $now = at();
        foreach ($names_male as $name){
            $id = DB::insert("INSERT IGNORE INTO `common_names` (`id`, `gender`, `text`, `language`, `country`, `created_at`) VALUES (NULL, 'm', '$name', 'it', NULL, '$now');");
            dd(DB::getLog(), "ID=$id");
        }

        foreach ($names_female as $name){
            $id = DB::insert("INSERT IGNORE INTO `common_names` (`id`, `gender`, `text`, `language`, `country`, `created_at`) VALUES (NULL, 'f', '$name', 'it', NULL, '$now');");
            dd(DB::getLog(),"ID=$id");
        }

        foreach ($surnames as $name){
            DB::insert("INSERT IGNORE INTO `common_surnames` (`id`, `text`, `language`, `country`, `created_at`) VALUES (NULL, \"$name\", 'it', NULL, '$now');");
        }
    }

    function parse(){
        $path = 'D:\www\Boctulus\Simplerest\etc\py-review-generator\answers.php';
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
            dd($row . " [$gender]", null, false);
        }

        return $rows;     
    }

    function insert_answers()
    {
        $prefix = Config::get()['tb_prefix'];

        $rows = $this->parse();

        foreach($rows as $row) {
            $comment    = $row;
            $score      = RandomGenerator::get([5 => 10, 7 => 100]);
            $clientName = ItalianReviews::getFullName();
            $createdAt  = now(); 

            // Incremento la probabilidad
            if (rand(1,10) > 3){
                $score = 5;
            }

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

    function update_by_gender(){
        $rows = table('star_rating')
        ->select(['id', 'comment', 'gender'])
        //->where(['id' => 69])  /////////////////////
        ->get();

        // dd($rows);

        foreach($rows as $row){
            $gender = ItalianGrammarAnalyzer::getGender($row['comment']);
            // dd($row['comment'] . "[$gender]", null, false);

            $author = ItalianReviews::getFullName($gender);

            /*
                No esta funcionando

                $id = DB::update("UPDATE star_rating SET gender='?', author='?' WHERE id=?", [$gender, $author, $row['id']]);
            */

            $author = str_replace("'", "\'", $author);

            $id = DB::update("UPDATE star_rating SET gender='$gender', author='$author' WHERE id={$row['id']}");
            // dd(DB::getLog(), "ID=$id");
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


    /*
        Testing
    */

    function test()
    {
        for ($i=0; $i<20; $i++){
            dd(ItalianReviews::getParaphrase('f'));
        }

        // $rows = $this->parse();

        // foreach($rows as $row) {
        //     $comment    = $row;
        //     $comment    = ItalianReviews::randomizePhrase($comment);

        //     dd($comment);
        // }
    }

    # php com review_generator reviews test_name_gen
    function test_name_gen(){
        for ($i=0; $i<500; $i++){
            print_r(
                ItalianReviews::getFullName() . PHP_EOL
            );
        }
    }

}


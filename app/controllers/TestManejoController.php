<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\XML;
use simplerest\core\Response;
use simplerest\core\libs\Files;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;

class TestManejoController extends Controller
{
    function __construct()
    {
        parent::__construct(); 

        css_file('practicatest.cl/bootstrap-3.4.1_min.css'); 
        css_file('practicatest.cl/style.themed.css');
    }

    function register_modal()
    {  
        ?> 
        <div class="container">
            <?php view('testmanejo/register_or_guest') ?>
        </div>
        <?php                     
    }

    function breadcrumb()
    {  
        ?> 
        <div class="container">
            <?php view('testmanejo/breadcrumb') ?>
        </div>
        <?php                     
    }

    function questions()
    {  
        ?> 
        <div class="container">
            <?php view('testmanejo/questions') ?>
        </div>
        <?php                     
    }

    /*
        *Buscar* preguntas en base de datos

        Sino existen, *guardar* con sus respuestas
    */
    function scrape()
    {  
        $html = Files::getContent('D:\www\simplerest\etc\practicatest\1.html');
    
        $dom   = XML::getDocument($html);
        $xpath = new \DOMXPath($dom);
    
        // Encontrar todos los elementos "div" con la clase que contiene "quest"
        $questions = $xpath->query('//div[contains(@class, "quest")]');
        foreach ($questions as $question) {
            $questionText = $question->getElementsByTagName('h4')->item(0)->textContent;
            dd("<h3>Pregunta: $questionText</h3>");

            // Encontrar las respuestas para esta pregunta
            $answers = $question->getElementsByTagName('li');
            foreach ($answers as $answer) {
                $answerText = trim($answer->textContent);
                if (!empty($answerText)) {
                    dd("Respuesta: $answerText");
                }
            }
        }
    }


}


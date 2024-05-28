<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\XML;
use simplerest\core\Response;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;

class XMLController extends Controller
{
    function test_xml_to_arr(){
        $str = "<ped><num>1234321</num><cli><rut>1-9</rut><nom>david lara oyarzun</nom><dir>los dominicos 7177</dir><gir>sin giro</gir><fon>899934523</fon><ema>dlara@runa.cl</ema><com>huechuraba</com></cli><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art></ped>";
        
        var_export(XML::toArray($str));
    }

    function test_arr_to_xml(){
        $arr = array (
            'num' => '1234321',
            'cli' =>
            array (
              'rut' => '1-9',
              'nom' => 'david lara oyarzun',
              'dir' => 'los dominicos 7177',
              'gir' => 'sin giro',
              'fon' => '899934523',
              'ema' => 'dlara@runasssssss.cl',
              'com' => 'huechuraba',
            ),
            'art' =>
            array (
              0 =>
              array (
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
              ),
              1 =>
              array (
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
              ),
              2 =>
              array (
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
              ),
            ),
        );

        /*
            Result:

           <root><num>1234321</num><cli><rut>1-9</rut><nom>david lara oyarzun</nom><dir>los dominicos 7177</dir><gir>sin giro</gir><fon>899934523</fon><ema>dlara@runa.cl</ema><com>huechuraba</com></cli><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art></root>
        */

        $result = XML::fromArray($arr, 'ped', false);

        return $result;
    }

    // author: stackoverflow user
    function test_html_replace(){
        $txt      = 'good <div class="good">good</div>';
        $search   = 'good';
        $replace  = 'nice';

        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHTML($txt, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query('//text()') as $text) {
            dd(trim($text->nodeValue));
            
            if (trim($text->nodeValue)) {
                $text->nodeValue = str_replace($search,$replace, $text->nodeValue);
            }
        }

        $html = $dom->saveHTML();
    }

    function test_html_replace_2(){
        // example of how to modify HTML contents
        require_once THIRD_PARTY_PATH . '/simple_html_dom_parser/simple_html_dom.php';

        Logger::truncate();

        // get DOM from URL or file
        $html = file_get_html(ROOT_PATH . '/tmp/woo.html');

        foreach($html->find('li') as $e){
            Logger::log($e->outertext);
        }
            

        // replace all input
        // foreach($html->find('input') as $e)
        //     $e->outertext = '[INPUT]';


        echo $html;
    }
}


<?php

namespace simplerest\controllers;

use simplerest\core\controllers\Controller;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;

class FbController extends Controller
{    
    function index(){
        $grupos = <<<STR
        WP | ES
        https://web.facebook.com/groups/313520438797399/
        https://web.facebook.com/groups/1758845590845832/
        https://www.facebook.com/groups/wpcrc
        https://www.facebook.com/groups/wptodos
        https://www.facebook.com/groups/AprendeWordpress
        https://www.facebook.com/groups/1523799777739104/
        https://www.facebook.com/groups/1031989716925981/
        https://www.facebook.com/groups/1290208751055228/
        https://web.facebook.com/groups/7094894653/
        https://web.facebook.com/groups/496610637551676/
        https://web.facebook.com/groups/ayudawp
        https://web.facebook.com/groups/140662189459719/
        https://web.facebook.com/groups/2813292022223049
        https://web.facebook.com/groups/wpargentina/
        https://web.facebook.com/groups/111122896249184/
        https://web.facebook.com/groups/1149775045474205/
        https://web.facebook.com/groups/1485989378282535/

        WOO | ES
        https://web.facebook.com/groups/450025545191592/
        https://web.facebook.com/groups/grupodewoocommerce
        https://web.facebook.com/groups/170857576608884/

        LearnDash | ES
        https://web.facebook.com/groups/780588969003381/

        PHP | ES
        https://www.facebook.com/groups/455489051242688/
        https://www.facebook.com/groups/DisenadoresYProgramadoresWeb
        https://www.facebook.com/groups/1528487033849008/
        https://www.facebook.com/groups/450005165750250/
        https://www.facebook.com/groups/706710869362657/
        https://www.facebook.com/groups/172707298378598/
        https://www.facebook.com/groups/1631769393748855/

        Programación | ES
        https://www.facebook.com/groups/771022723278866/
        https://www.facebook.com/groups/Programadores.Ecuador
        https://www.facebook.com/groups/1740670476049420
        https://www.facebook.com/groups/378513999208093
        https://www.facebook.com/groups/1216288588385133/
        https://www.facebook.com/groups/1656457771305611/
        https://web.facebook.com/groups/311323871209420/
        https://web.facebook.com/groups/1051533001572433/

        Testing y QA | ES
        https://www.facebook.com/groups/1616115831782049/

        WP | BR
        https://www.facebook.com/groups/348202828937333/
        https://www.facebook.com/groups/515803728487598/
        https://www.facebook.com/groups/519894688029678/
        https://www.facebook.com/groups/322479461553529/

        PHP | BR
        https://www.facebook.com/groups/142151625841770/

        WP - WOO | IT
        https://www.facebook.com/groups/1380949515301271/
        https://www.facebook.com/groups/wpitalyplus/

        PHP | IT
        https://www.facebook.com/groups/377041959088599
        https://www.facebook.com/groups/59356989650/
        https://www.facebook.com/groups/336426883084246/

        Programación | IT
        https://www.facebook.com/groups/353299598353917/

        Programación | RO (Rumanía)
        https://www.facebook.com/groups/677877235598824/
        https://www.facebook.com/groups/619869691520337/
        https://www.facebook.com/groups/867361626633166/

        C# .NET
        https://www.facebook.com/groups/304179163001281/
        STR;

        $sections = [];
        $lines = Strings::lines($grupos);

        $current_section = '';
        $current_links = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            
            $line = str_replace('https://www.', 'https://web.', $line);
            
            if (strpos($line, 'http') === false) {
                // Es un título de sección
                if (!empty($current_section)) {
                    $sections[$current_section] = $current_links;
                }
                $current_section = $line;
                $current_links = [];
            } else {
                // Es un enlace
                $current_links[] = $line;
            }
        }

        // Eliminar enlaces duplicados en cada seccion
        foreach ($sections as $ix => $section){
            $sections[$ix] = array_unique($section);
        }

        // Añadir la última sección
        if (!empty($current_section)) {
            $sections[$current_section] = $current_links;
        }

        css_file(VIEWS_PATH . 'fb_groups/css/styles.css');
        view('fb_groups\fb_groups.php', ['sections' => $sections], 'templates/tpl_basic.php');
    }
}


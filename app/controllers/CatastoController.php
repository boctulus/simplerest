<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class CatastoController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {

    $str = <<<STR
  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A11%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A11%3A38%22%2C%22id%22%3A%22644a907352e71b3f471901be%22%2C%22cf_piva%22%3A%22LCHGLN49R07F381F%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608243%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22LCHGLN49R07F381F%22%2C%22utenze%22%3A%5B%223482420747%22%2C%223472811962%22%2C%22065124486%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A11%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A12%3A01%22%2C%22id%22%3A%22644a9085a9c4261a5c76f5e8%22%2C%22cf_piva%22%3A%22PLNNZE47B16F591D%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608261%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22PLNNZE47B16F591D%22%2C%22utenze%22%3A%5B%223282497484%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A12%3A01%22%2C%22date_completion%22%3A%222023-04-27+17%3A12%3A16%22%2C%22id%22%3A%22644a90980fa0590dca5419f2%22%2C%22cf_piva%22%3A%22VLIPTR63P28G388M%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608280%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22VLIPTR63P28G388M%22%2C%22utenze%22%3A%5B%223429041756%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A12%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A12%3A24%22%2C%22id%22%3A%22644a90b252e71b3f471901bf%22%2C%22cf_piva%22%3A%22DPRCML62S26C351R%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608306%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22DPRCML62S26C351R%22%2C%22utenze%22%3A%5B%223500880386%22%2C%223451595194%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A13%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A13%3A40%22%2C%22id%22%3A%22644a90c8ab8d551e731935b8%22%2C%22cf_piva%22%3A%22BNVGNN65S47C351Z%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608328%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22BNVGNN65S47C351Z%22%2C%22utenze%22%3A%5B%223408207012%22%2C%223403648361%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A13%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A13%3A56%22%2C%22id%22%3A%22644a90d9efe0a35a8820bf09%22%2C%22cf_piva%22%3A%22PLLPLN50E03A161H%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608345%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22PLLPLN50E03A161H%22%2C%22utenze%22%3A%5B%223384938960%22%2C%223938862423%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A14%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A14%3A38%22%2C%22id%22%3A%22644a910622d63642b010f7b6%22%2C%22cf_piva%22%3A%22WFNNTN56R31H858L%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608389%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22WFNNTN56R31H858L%22%2C%22utenze%22%3A%5B%220471354078%22%5D%7D%7D

  data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A14%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A14%3A50%22%2C%22id%22%3A%22644a9118b586ec1d84491a59%22%2C%22cf_piva%22%3A%22GBTGTR30C19H890H%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608408%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22GBTGTR30C19H890H%22%2C%22utenze%22%3A%5B%5D%7D%7D
STR;
     
    $reqs = explode('data=', trim($str));
     
    foreach($reqs as $ix => $req){
        $str = trim($req);

        if (empty($str)){
            continue;
        }

        $str = urldecode($str);

        $str = json_decode($str, true);
        
        dd($str['soggetto']['utenze'], $str['cf_piva']. $ix);

    }
                       
    }
}


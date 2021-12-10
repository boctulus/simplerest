<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;

	/*
            Borrar la carpeta /docs/dev

    */


    $ok = Files::delTree(ROOT_PATH . 'docs/dev');
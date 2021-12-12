<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\libs\DB;

	/*
        Borrar todos los schemas de la vieja ubicación (dentro de models)

    */

    $ok = Files::delTree(MODELS_PATH . 'schemas', true);
    d($ok, 'Schema directory cleared');
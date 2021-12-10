<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\libs\DB;

	/*
        Borrar todos los schemas

    */

    $ok = Files::delTree(SCHEMA_PATH, true);
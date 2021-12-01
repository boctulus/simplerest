<?php

	/*
        Borrar todos los schemas

    */

    $ok = Files::deleteAll(SCHEMA_PATH, '*.php');
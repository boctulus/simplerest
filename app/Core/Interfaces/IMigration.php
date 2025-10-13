<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface IMigration {

    /**
     * Run migration
     *
     * @return void
     */
    function up();

}
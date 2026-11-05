<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Interfaces;

interface IMigration {

    /**
     * Run migration
     *
     * @return void
     */
    function up();

}
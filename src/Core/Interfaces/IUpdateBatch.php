<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface IUpdateBatch {

    /**
     * Run migration
     *
     * @return void
     */
    function run() : ?bool;
}
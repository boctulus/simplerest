<?php

namespace simplerest\core\interfaces;

interface IUpdateBatch {

    /**
     * Run migration
     *
     * @return void
     */
    function run() : ?bool;
}
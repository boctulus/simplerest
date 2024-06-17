<?php

namespace simplerest\core\interfaces;

interface ICommand {
    function handle($args);
}
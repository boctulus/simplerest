<?php

namespace simplerest\core\interfaces;

interface ICommand {
    function handle($args);
    function help($name = null, ...$args);
}
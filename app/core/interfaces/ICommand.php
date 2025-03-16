<?php

namespace simplerest\core\interfaces;

/*
    Posibilidad de mejoras

    https://chatgpt.com/c/67d6ed9d-7454-800d-a9be-395095c69518
*/

interface ICommand {
    function handle($args);
    function help($name = null, ...$args);
}
<?php

namespace simplerest\interfaces;

interface ICommand {
    function handle($args);
}
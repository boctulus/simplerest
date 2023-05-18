<?php

namespace simplerest\core\interfaces;

interface IValidator {
    function validate(array $data, array $rules, $fillables = null, $not_fillables = null);
    function getErrors() : array;
}
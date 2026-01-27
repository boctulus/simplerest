<?php

namespace Boctulus\Simplerest\Core\Interfaces;

interface IValidator {
    function validate(array $data, array $rules, $fillables = null, $not_fillables = null);
    function getErrors() : array;
}
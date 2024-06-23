<?php

use simplerest\core\libs\Options;

/**
 * Retrieves the value of a given key from the options table.
 * If the value is serialized, it will be deserialized before returning.
 *
 * @param string $key The key for the option to retrieve.
 * @return mixed The value of the option, deserialized if necessary.
 */
function get_option(string $key) {
    return Options::getOption($key);
}

/**
 * Sets the value of a given key in the options table.
 * The value will be serialized before storing.
 *
 * @param string $key The key for the option to set.
 * @param mixed $val The value to set, which will be serialized.
 * @return bool Whether the operation was successful.
 */
function set_option(string $key, $val) {
    return Options::setOption($key, $val);
}

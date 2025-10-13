<?php

namespace Boctulus\Simplerest\Core\Libs;

class Options
{    
    /**
     * Retrieves the value of a given key from the options table.
     * If the value is serialized, it will be deserialized before returning.
     *
     * @param string $key The key for the option to retrieve.
     * @return mixed The value of the option, deserialized if necessary.
     */
    static function getOption(string $key) {
        $value = table('options')
            ->where(['the_key' => $key])
            ->value('the_val');

        // Attempt to unserialize the value
        $unserializedValue = @unserialize($value);

        // Return the unserialized value if deserialization was successful
        return $unserializedValue === false && $value !== 'b:0;' ? $value : $unserializedValue;
    }

    /**
     * Sets the value of a given key in the options table.
     * The value will be serialized before storing.
     *
     * @param string $key The key for the option to set.
     * @param mixed $val The value to set, which will be serialized.
     * @return bool Whether the operation was successful.
     */
    static function setOption(string $key, $val) {
        // Serialize the value
        $serializedVal = serialize($val);

        if (get_option($key) === false) {
            return table('options')
                ->noValidation()
                ->insert([
                    'the_key' => $key,
                    'the_val' => $serializedVal
                ]);
        } else {
            return table('options')
                ->noValidation()
                ->where(['the_key' => $key])
                ->update([
                    'the_val' => $serializedVal
                ]);
        }
    }
}


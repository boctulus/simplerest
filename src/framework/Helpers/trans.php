<?php

/*
    Translation entry point used by the core (validation and HTTP error messages).

    boctulus/simplerest-i18n defines the real trans() through Composer's
    autoload "files", which load before these helpers. Without that package
    the text is returned untranslated instead of a fatal "undefined function".
*/
if (!function_exists('trans')) {
    function trans(string $text, $domain = null) {
        return $text;
    }
}

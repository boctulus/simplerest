<?php

namespace simplerest\core\libs;

use Sabberworm\CSS\Parser;

class CSSUtils {
    
    static function beautifier(string $path) {
        $parser = new Parser(file_get_contents($path));

        $css            = $parser->parse();
        $desminifiedCSS = $css->render();

        return $desminifiedCSS;
    }
    
    static function getCSSRules(string $path, array $css_classes) {
        $css   = static::beautifier($path);
        $rules = Strings::lines($css, true);
        
        foreach ($rules as $ix => $rule){
            if (!Strings::containsAny($css_classes, $rule)){
                unset($rules[$ix]);
            }
        }

        return array_values($rules);
    }
    
}


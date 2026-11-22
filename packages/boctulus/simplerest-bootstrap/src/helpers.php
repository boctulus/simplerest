<?php

declare(strict_types=1);

use Boctulus\Simplerest\HtmlBuilder\Tag;

// Moved from the SimpleRest core helpers (src/framework/Helpers/factories.php) on 2026-11-22:
// Tag is distributed by this package, not by the core.
if (!function_exists('tag')) {
    function tag(string $name): Tag
    {
        return new Tag($name);
    }
}

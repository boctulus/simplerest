<?php

namespace Boctulus\Zippy\Libs;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;

class CategoryUtils
{
    /**
     * Given a category id, returns breadcrumb string from root to the category.
     * Example: "A > A2 > A2-1 > A2-1a"
     *
     * @param mixed $cat_id Category id (string or int)
     * @param string $separator
     * @return string
     */
    public static function breadcrumb($cat_id, string $separator = ' > '): string
    {
        // Protect against malformed input
        if ($cat_id === null || $cat_id === '') {
            return '';
        }


        $visited = [];
        $path = [];
        $current = $cat_id;
        $maxDepth = 200; // safe-guard against cycles
        $depth = 0;


        while ($current !== null && $current !== '' && $depth < $maxDepth) {
            // Prevent infinite loops if data contains cycles
            if (in_array((string)$current, $visited, true)) {
                break;
            }


            $visited[] = (string)$current;


            $row = table('categories')
                ->select('id', 'name', 'parent_id')
                ->where('id', $current)
                ->first();


            if (!$row) {
                // If the current id is not found, stop traversing
                break;
            }


            // Prepend name to path later by collecting then reversing
            $path[] = $row['name'];


            // Move to parent
            $parent = $row['parent_id'];


            // If parent is null, empty string or 0 (depending on schema), we reached root
            if ($parent === null || $parent === '' || $parent === 0 || $parent === '0') {
                break;
            }


            $current = $parent;
            $depth++;
        }


        if (empty($path)) {
            return '';
        }


        $path = array_reverse($path);


        return implode($separator, $path);
    }
}

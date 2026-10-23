<?php

namespace Boctulus\Simplerest\CodeTools\Cleaners;

use Boctulus\Simplerest\Core\Libs\HTML;
use Boctulus\Simplerest\Core\Libs\Files;

class AngularCleaner
{
     /*
        Elimina vestigios de Angular / AngularJS
        de un HTML
    */
    static function remove($page)
    {
        if (file_exists($page)){
            $page = Files::getContent($page);
        }

        $page = HTML::removeHTMLAttributes($page, [
            'data-ng-click',
            'data-ng-if',
            'data-ng-class',
            'data-ng-repeat',
            'data-ng-model',
            'data-ng-show',
            'data-ng-options',
            'data-ng-maxlength',
            'data-ng-messages',
            'data-ng-bind',
            'data-ng-disabled',
            'data-ng-value',
            'lang-bind',
            'data-toggle',
            'lang-bind-attr'
        ]);

        $page = str_replace('ng-hide', 'd-none', $page);

        $page = self::removeClasses($page, [
            'ng-pristine',
            'ng-untouched',
            'ng-valid',
            'ng-not-empty',
            'ng-inactive'
        ]);

        $page = HTML::removeEmptyAttributes($page);

        return $page;
    }

    private static function removeClasses(string $html, array $classesToRemove): string
    {
        $classesToRemove = array_fill_keys($classesToRemove, true);

        return preg_replace_callback(
            '/(?<![A-Za-z0-9_:-])(class\s*=\s*)(["\'`])([^"\'`]*)\\2/i',
            static function (array $match) use ($classesToRemove): string {
                $classes = preg_split('/\s+/', trim($match[3]), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $remainingClasses = array_values(array_filter(
                    $classes,
                    static fn(string $class): bool => !isset($classesToRemove[$class])
                ));

                if (count($remainingClasses) === count($classes)) {
                    return $match[0];
                }

                return $match[1] . $match[2] . implode(' ', $remainingClasses) . $match[2];
            },
            $html
        ) ?? $html;
    }

}

<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\HTML;
use Boctulus\Simplerest\Core\Libs\Strings;

/*
    HTML minifier for scraped pages. Formerly Strings::minimifyHTML().
*/
class HtmlMinifier
{
	/*
		0 < $level < 8
	*/
	static function minify($html, int $level = 5) : string {
		if ($level >= 7){
			$html = HTML::stripTag($html, 'nav');
            $html = HTML::stripTag($html, 'img');
			$html = HTML::stripTag($html, 'footer');
			$html = HTML::HTML2Text($html);

			// Replace multiple (one ore more) line breaks with a single one.
			$html = preg_replace("/[\r\n]+/", "\n", $html);
									
			return $html;
		}

		$html = Strings::removeHTMLentities($html);
		$html = HTML::stripTag($html, 'head');
		$html = HTML::stripTag($html, 'footer');
		$html = HTML::stripTag($html, 'script');
		$html = HTML::stripTag($html, 'style');
		$html = HTML::stripTag($html, 'iframe');
		$html = HTML::stripTag($html, 'svg');		
		$html = HTML::removeHTMLAttributes($html, [
			'onclick',
			'ondblclick',
			'onmousedown',
			'onmouseup',
			'onmousemove',
			'onmouseover',
			'onmouseout',
			'onkeydown',
			'onkeyup',
			'onkeypress',
			'onfocus',
			'onblur',
			'onchange',
			'onsubmit',
			'onreset',
			'onselect',
			'oninput',
			'onload',
			'onunload',
			'onerror',
			'onresize',
			'onscroll'
		]);

		$html = HTML::removeHTMLAttributes($html, ['style', 'class', 'rel', 'target', 'type']);
		$html = Strings::removeMultiLineComments($html);
		$html = static::removeCSS($html);

		if ($level >=2){
			$html = HTML::removeComments($html);
		}

		if ($level >=3){
			$html = Strings::removeHTMLTextModifiers($html);
		}

		if ($level >= 4){
			$html = static::removeCSSClasses($html);			
		}

		if ($level >= 5){
			$html = Strings::removeDataAttr($html); // bye data-*
		}

		$html = Strings::removeSpaceBetweenTags($html);
		$html = Strings::removeMultipleSpacesInLines($html);
		$html = Strings::wipeEmptyTags($html);		

		
		$html = Strings::afterIfContains($html, '<body>');

		if (Strings::contains('</body>', $html)){
			$html = Strings::beforeLast($html, '</body>');
		}	

		return $html;		
	}

    static function removeCSSClasses(string $html, $classesToRemove = null) : string {
        if (!empty($classesToRemove)) {
            $classesToRemove = array_fill_keys(
                array_map(static fn($class): string => trim((string) $class), $classesToRemove),
                true
            );

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

        return preg_replace_callback('/<[^<>]*\sclass=[\'"`][^\'"`]*[\'"`][^<>]*>/i', function($match) {
            return preg_replace('/\sclass=[\'"`][^\'"`]*[\'"`]/i', '', $match[0]);
        }, $html);        
    }

    static function removeCSS(string $page, bool $remove_style_sections = true, bool $remove_css_inline = true) : string
     {
		// Eliminar CSS entre etiquetas <style></style>
		if ($remove_style_sections) {
			$page = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $page);
		}
		
		// Eliminar CSS inline dentro del atributo style=""
		if ($remove_css_inline) {
			$page = preg_replace('/style="[^"]*"/i', '', $page);
		}

        /*
            Elimino class="" vacios
        */

        $page = str_replace('class=""', '', $page);     
		
		return $page;
	}
}

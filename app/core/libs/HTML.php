<?php

namespace simplerest\core\libs;

use simplerest\core\libs\XML;
use simplerest\core\libs\Url;

class HTML extends XML
{
    /**
     * Convert relative URLs in <img src=""> and <a href=""> to absolute URLs.
     *
     * @param string $html The HTML content.
     * @param string $root The root URL to be prepended to relative URLs.
     * @return string The updated HTML content with absolute URLs.
     */
    public static function relativeToAbsoluteURLs(string $html, string $root): string
    {
        $dom   = XML::getDocument($html);
        $xpath = new \DOMXPath($dom);

        // Update <img src="">
        $imgNodes = $xpath->query('//img[@src]');
        foreach ($imgNodes as $img) {
            $src = $img->getAttribute('src');
            if (Url::isRelativeURL($src)) {
                $img->setAttribute('src', rtrim($root, '/') . '/' . ltrim($src, '/'));
            }
        }

        // Update <a href="">
        $aNodes = $xpath->query('//a[@href]');
        foreach ($aNodes as $a) {
            $href = $a->getAttribute('href');
            if (Url::isRelativeURL($href)) {
                $a->setAttribute('href', rtrim($root, '/') . '/' . ltrim($href, '/'));
            }
        }

        return $dom->saveHTML();
    }

    /*
        Devuelve un array con todos los IDs utilizados en el documento HTML

        Puede ser útil para identificar elementos específicos en el DOM.

        Ej:

            $html = Files::getContent('D:\Desktop\EAT-LEAF (NICK)\#SECTIONS\section-1.html');
            $ids  = HTMLTools::getIDs($html);

        Salida:

            Array
            (
                [0] => I5yDDTg9dkNxvJNF
                [1] => XGGspzT0WdnBFWbQ
                ...
            )
    */
    static function getIDs(string $html) {
        $dom = XML::getDocument($html);

        // Obtenemos todos los elementos con atributo 'id'
        $idNodes = $dom->getElementsByTagName('*');
        $ids = [];

        foreach ($idNodes as $node) {
            if ($node->hasAttribute('id')) {
                $ids[] = $node->getAttribute('id');
            }
        }

        return $ids;
    }

    
    public static function HTML2Text(string $page): string {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); 
        $dom->loadHTML($page);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $tagsWithLineBreak = ['div', 'br', 'p']; // Agrega aquí los tags adicionales que deseas

        // Elimina los elementos de script y style
        $scripts = $xpath->query('//script|//style');
        foreach ($scripts as $script) {
            $script->parentNode->removeChild($script);
        }

        $text = '';
        $nodes = $xpath->query('//text()');
        foreach ($nodes as $node) {
            $parentNode = $node->parentNode;
            if (in_array(strtolower($parentNode->nodeName), $tagsWithLineBreak)) {
                $text .= $node->nodeValue . "\r\n";
            } else {
                $text .= $node->nodeValue;
            }
        }

        $text = Strings::trimMultiline($text);

        return $text;
    }

    /*
        https://davidwalsh.name/remove-html-comments-php

        Not working as expected
    */
    static function removeComments(string $html) : string {
        return preg_replace('/<!--(^-->)*?-->/', '', $html);
    }

    /*
		Puede usarse para remover <head>, <footer>, <style> y <script>

        Es recomendable usar stripTag() en su lugar

        $page string
        $tag  array|string

        @return string
	*/
	public static function removeTags(string $page, $tag) : string {
        if (is_string($tag)) {
            // Si se proporciona un solo tag como string, convertirlo a un array de un solo elemento
            $tag = [$tag];
        }

        foreach ($tag as $t) {
            $pattern = "/<$t\b[^>]*>(.*?)<\/$t>/si";
            $page = preg_replace($pattern, '', $page);
        }

        return $page;
    }
    

	/*
		Util para eliminar eventos de JS y atributos como style y class

        $page string
        $attr array|string

        @return string
	*/
	public static function removeHTMLAttributes(string $page, $attr = null) : string {
        // if ($attr === null) {
        //     // Si no se proporciona ningún atributo, eliminar todos los atributos en las etiquetas
        //     return preg_replace('/\s*([a-z]+\s*=\s*"[^"]*"|([a-z]+\s*=\s*\'[^\']*\'))/i', '', $page);
        // }   

        /*
            Eliminar todas las ocurrencias del atributo o atributos especificados.

            Ej:

                XML::removeHTMLAttributes($html, 'onclick');
                XML::removeHTMLAttributes($html, ['style', 'class']);

            Nota:

            Debe ser insensible a mayúsculas y minúsculas. Ej: "onClick" y "onclick"
        */

        // Convertir el atributo o atributos a un array si es un string
        if (!is_array($attr)) {
            $attr = [$attr];
        }

        // Recorrer los atributos y eliminar todas las ocurrencias en la página
        foreach ($attr as $attribute) {
            $page = preg_replace("/$attribute=\"[^\"]*\"/i", '', $page);
            $page = preg_replace("/$attribute='[^']*'/i", '', $page);
        }

        return $page;
    }

    static function stripTagByClass(string $page, string $class): string {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($page, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        // Crea un objeto DOMXPath para buscar por clase de CSS
        $xpath = new \DOMXPath($dom);
        
        // Encuentra todos los elementos con la clase especificada
        $elements = $xpath->query("//*[@class='$class']");
        
        // Elimina cada elemento encontrado
        foreach ($elements as $element) {
            $parent = $element->parentNode;
            $parent->removeChild($element);
        }
        
        // Obtén el XML resultante como string
        $newPage = static::saveXMLWithoutHeader($dom);
        
        return $newPage;
    }

    /*
        Funciona mejor que otras alternativas como

        $css_links = HTML::extractLinksByRelType($html, "stylesheet", "css");
    */
    static function getCSSLinks($content){
        $links = XML::extractNodes($content, '//link', 'href');

        $css_links = [];
        foreach ($links as $link){
            if (Strings::contains('.css', $link)){
                $css_links[] = $link;
            }
        }

        return $css_links;
    }

    static function extractLinkUrls(string $html, $extension = null, bool $include_query_params = true) {
        $urls = [];
    
        $dom = static::getDocument($html);
    
        $linkElements = $dom->getElementsByTagName('link');
    
        foreach ($linkElements as $linkElement) {
            $href = $linkElement->getAttribute('href');            
            $ext  = Files::getExtension($href);

            if (!empty($href)) {
                if ($extension === null || Files::matchExtension($ext, $extension)) {
                    if ($include_query_params) {
                        $urls[] = $href;
                    } else {
                        $parsedUrl = parse_url($href);
                        $urlWithoutQuery = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
                        $urls[] = $urlWithoutQuery;
                    }
                }
            }
        }
    
        return $urls;
    }

    static function removeEmptyAttributes($html) {
        // Encuentra y reemplaza atributos vacíos
        $html = preg_replace('/\s+(\w+)\s*=\s*["\']\s*["\']/', '', $html);
        return $html;
    }

    static function replacePHPmarkers($html) {
        $html = str_replace('<?php', '<!-- PHP_INI -->', $html);
        $html = str_replace('<?=', '<!-- PHP_INI echo -->', $html);
        $html = str_replace('?>', '<!-- PHP_END -->', $html);

        return $html;
    }

    static function replacePHPmarkersBack($html) {
        $html = str_replace('<!-- PHP_INI -->', '<?php', $html);
        $html = str_replace('<!-- PHP_END -->', '?>', $html);
        $html = str_replace('<?php echo', '<?=', $html);
    
        return $html;
    }    

    /*
        $html string
        $tags array|string|null

        @return string
    */
    static function removeHTMLTextModifiers(string $html, $tags = null): string {
		$dom   = static::getDocument($html);
		$xpath = new \DOMXPath($dom);  // HERE
	
		$tagsToRemove = ['b', 'i', 'u', 's', 'strong', 'em', 'sup', 'sub', 'mark', 'small'];
	
		if (is_array($tags) || is_string($tags)) {
			// Si se proporciona un array o una cadena de etiquetas, se utilizan en lugar de las predeterminadas
			$tagsToRemove = is_array($tags) ? $tags : [$tags];
		}
	
		foreach ($tagsToRemove as $tag) {
			$elements = $xpath->query("//{$tag}");
			foreach ($elements as $element) {
				$parent = $element->parentNode;
				while ($element->firstChild) {
					$parent->insertBefore($element->firstChild, $element);
				}
				$parent->removeChild($element);
			}
		}
	
		$output = static::saveXMLWithoutHeader($dom);
	
		// Eliminar la etiqueta <!DOCTYPE> y la envoltura <html><body> agregadas por DOMDocument
		// $output = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $output);
	
		return $output;
	}

    static function removeSocialLinks($html) {
        // Array de redes sociales conocidas y sus dominios
        $socialNetworks = array(
            'facebook.com',
            'twitter.com',
            'instagram.com',
            'linkedin.com'
            // Agrega más redes sociales y dominios si lo deseas
        );
    
        // Construye la expresión regular para buscar los enlaces a redes sociales conocidas
        $pattern = '/<a.*href=["\']https?:\/\/(www\.)?(%s)\/.*["\'].*>.*<\/a>/i';
        $pattern = sprintf($pattern, implode('|', $socialNetworks));
    
        // Remueve los enlaces a redes sociales conocidas
        $html = preg_replace($pattern, '', $html);
    
        return $html;
    }

    /*
        Procesa un archivo de vista y agrega referencias al traductor
    */
    static function insertTranslator($html) {
        $dom   = static::getDocument($html);
        $xpath = new \DOMXPath($dom);
        
        $nodes = $xpath->query('//text()');

        foreach ($nodes as $node) {
            $text = trim($node->nodeValue);
            if (!empty($text)) {
                $newNode = $dom->createCDATASection('<?= trans("' . $text . '") ?>');
                $node->parentNode->replaceChild($newNode, $node);
            }
        }

        $html = $dom->saveHTML();

        return $html;
    }

    /*
        Devuelve ocurrencias de <article>

        Ej:

        $html = Files::getContent(ETC_PATH . 'page.html');        
        $html = XML::getArticles($html) ?? $html;

        dd($html);
    */
    static function getArticles(string $html, bool $as_string = true){
        $arts = XML::extractNodes($html, '//article');

        if (empty($arts)){
            return;
        }

        if ($as_string){
            $arts = implode("\r\n\r\n", $arts);
        }   

        return $arts;
    }

}


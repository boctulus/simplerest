<?php

namespace simplerest\core\libs;

class XML
{
    /*
        The intend is to get the "DOM selector" given a text which should be found as substring of a text node. The problem is an error.
    */
    public static function getSelector(string $html, string $text) : string {
            $dom = static::getDocument($html);

            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query("//text()[contains(., '$text')]/parent::*");

            $selector = '';
            foreach ($nodes as $node) {
                $selector .= self::getNodeSelector($node) . '/';
            }

            $selector = rtrim($selector, '/');

            return $selector;
        }

        private static function getNodeSelector(\DOMNode $node) : string {
        $selector = '';

        while ($node && $node->nodeType === XML_ELEMENT_NODE) {
            $nodeName = $node->nodeName;
            $nodeIndex = self::getNodeIndex($node);
            $selector = "{$nodeName}[{$nodeIndex}]{$selector}"; // Update the order of concatenation

            $node = $node->parentNode; // Move to the parent node
        }

        $selector = '/' . rtrim($selector, '/');

        return $selector;
    }
 

    private static function getNodeIndex(\DOMNode $node) : int {
        $index = 1;
        $previousNode = $node->previousSibling;

        while ($previousNode) {
            if ($previousNode->nodeName === $node->nodeName) {
                $index++;
            }
            $previousNode = $previousNode->previousSibling;
        }

        return $index;
    }

    /*
        Ej:

        $selector = '//p';
        $result = XML::getTag($html, $selector);
        dd($result, $selector); 
    
        $selector = '//div[contains(@class, "my_class")]';
        $result = XML::getTag($html, $selector);
        dd($result, $selector); 
    */
    static function getTag(string $html, string $selector): array {
        $dom = static::getDocument($html);
        
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query($selector);

        $result = array();
        foreach ($nodes as $node) {
            $result[] = $dom->saveXML($node);
        }

        return $result;
    }

    /*
        Devuelve ocurrencias de <article>

        Ej:

        $html = Files::getContent(ETC_PATH . 'page.html');        
        $html = XML::getArticles($html) ?? $html;

        dd($html);
    */
    static function getArticles(string $html, bool $as_string = true){
        $arts = XML::query($html, '//article');

        if (empty($arts)){
            return;
        }

        if ($as_string){
            $arts = implode("\r\n\r\n", $arts);
        }   

        return $arts;
    }

    // alias
    static function query(string $html, string $selector): array {
        return static::getTag($html, $selector);
    }
    
    static function saveXMLNoHeader(\DOMDocument $dom) : string {
        $str = $dom->saveXML();

        $str = Strings::afterIfContains($str, '<?xml version="1.0" standalone="yes"?>');
        $str = Strings::afterIfContains($str, '<?xml encoding="UTF-8"?>');
        $str = static::stripDOCTYPE($str);

        return ltrim($str);
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
    
    static function toArray(string $xml){
        $xml = trim($xml);

        libxml_use_internal_errors(true);

        $parser = xml_parser_create();
        
        xml_parse_into_struct($parser, $xml, $vals, $index);
        xml_parser_free($parser);

        $tree = array();
        $refs = array();
        foreach ($vals as $xml_elem) {
            $tag = $xml_elem['tag'];
            $level = $xml_elem['level'];
            if ($xml_elem['type'] == 'open') {
                $tree[$level][$tag][] = isset($xml_elem['attributes']) ? $xml_elem['attributes'] : array();
                $cur = &$tree[$level][$tag][count($tree[$level][$tag]) - 1];

                if (isset($xml_elem['value'])) {
                    $cur['_value'] = $xml_elem['value'];
                }
                
                if (isset($xml_elem['attribute']['ID'])){
                    $refs[$xml_elem['attribute']['ID']] = &$cur;
                }

            } elseif ($xml_elem['type'] == 'complete') {
                $tree[$level][$tag][] = isset($xml_elem['value']) ? ($xml_elem['value']) : array();
            } elseif ($xml_elem['type'] == 'close') {
                $current = &$tree[$level - 1];
                $current[$tag . '_assoc'] = $tree[$level][$tag];
                unset($tree[$level][$tag]);
            }
        }
        
        return $tree;
    }  

    /*
        Requiere del paquete de Composer spatie/array-to-xml

        composer require spatie/array-to-xml
    */
    static function fromArray(array $arr, string $root_elem = 'root', $header = true){
        if (!\Composer\InstalledVersions::isInstalled('spatie/array-to-xml')){
            throw new \Exception("Composer package spatie/array-to-xml is requiered");
        }

        if (!class_exists(\Spatie\ArrayToXml\ArrayToXml::class)){
            throw new \Exception("Class not found");
        } else {
            $class = "\Spatie\ArrayToXml\ArrayToXml";
            $converter = new $class($arr, $root_elem);
        }

        $result = $converter::convert($arr, $root_elem, $header);

        if (!$header){
            $result = trim(substr($result, 21));
        }

        return $result;
    }
    
    static function getDocument(string $html){
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);

        if (!Strings::contains('<?xml encoding="UTF-8">', $html)){
            $html = '<?xml encoding="UTF-8">' . $html;
        }
        
        $doc->loadHTML($html);       
        libxml_use_internal_errors(false);

        return $doc;
    }  
    
    static function getXPath(string $html){
        return new \DOMXPath(
            static::getDocument($html)
        );
    }   

    // Recupera textos de nodos
    static function getTextFromNodes($html) {
        $dom   = static::getDocument($html);
        $xpath = new \DOMXPath($dom);
        
        $nodes = $xpath->query('//text()');

        $textNodes = [];
        foreach ($nodes as $node) {
            $text = trim($node->nodeValue);
            if (!empty($text)) {
                $textNodes[] = $text;
            }
        }

        return $textNodes;
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
	
		$output = static::saveXMLNoHeader($dom);
	
		// Eliminar la etiqueta <!DOCTYPE> y la envoltura <html><body> agregadas por DOMDocument
		// $output = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $output);
	
		return $output;
	}

    /*
        Puede remover cualquier <tag> de HTML 
        sin hacer uso de regex

        Remueve tambien sus hijos

        https://stackoverflow.com/a/7131156/980631
    */
    static function stripTag(string $html, $tag) : string {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $script = $dom->getElementsByTagName($tag);

        $remove = [];
        foreach($script as $item){
            $remove[] = $item;
        }

        foreach ($remove as $item){
            $item->parentNode->removeChild($item); 
        }

        $html = static::saveXMLNoHeader($dom);
        
        return $html;
    }

    static function stripTagById(string $page, string $id): string {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($page, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Obtén el elemento con el ID especificado
        $element = $dom->getElementById($id);
        
        if ($element) {
            // Obtén el padre del elemento y elimina el elemento del árbol DOM
            $parent = $element->parentNode;
            $parent->removeChild($element);
        }
        
        // Obtén el XML resultante como string
        $newPage = static::saveXMLNoHeader($dom);
        
        return $newPage;
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
        $newPage = static::saveXMLNoHeader($dom);
        
        return $newPage;
    }

    static function stripXMLTags(string $str): string {
        $str = Strings::afterIfContains($str, '<?xml version="1.0" standalone="yes"?>');
        $str = Strings::afterIfContains($str, '<?xml encoding="UTF-8"?>');

        return trim($str);
    }

    public static function HTML2Text(string $page): string {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suprime los errores de HTML mal formado
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
    
    static function stripDOCTYPE(string $page) : string {
        $pattern = '/<!DOCTYPE[^>]+>/i';
        $stripped_page = preg_replace($pattern, '', $page);
        return $stripped_page;
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

    static function removeEmptyAttributes($html) {
        // Encuentra y reemplaza atributos vacíos
        $html = preg_replace('/\s+(\w+)\s*=\s*["\']\s*["\']/', '', $html);
        return $html;
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
    
    /**
     * Extract links by their rel type.
     *
     * @param string $html The HTML content to parse.
     * @param string|array $rel_type The rel type(s) of links to extract.
     * @param string|null $extension Extension to filter URLs (only for rel_type "stylesheet").
     * @param bool $include_query_params Include query parameters in URLs.
     * @return array An array of extracted links.
     */
    static function extractLinksByRelType(string $html, $rel_type, $extension = null, bool $include_query_params = true) {
        $urls = [];

        $dom = static::getDocument($html);

        $linkElements = $dom->getElementsByTagName('link');

        foreach ($linkElements as $linkElement) {
            $linkRel = $linkElement->getAttribute('rel');
            $linkRel = explode(' ', $linkRel); // Split rel attribute into an array of rel types

            if (!empty(array_intersect((array)$rel_type, $linkRel))) {
                $href = $linkElement->getAttribute('href');
                if (!empty($href)) {
                    if ($rel_type === 'stylesheet') {
                        $ext = Files::getExtension($href);
                        if ($extension === null || Files::matchExtension($ext, $extension)) {
                            if ($include_query_params) {
                                $urls[] = $href;
                            } else {
                                $parsedUrl = parse_url($href);
                                $urlWithoutQuery = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
                                $urls[] = $urlWithoutQuery;
                            }
                        }
                    } else {
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
        }

        return $urls;
    }


}


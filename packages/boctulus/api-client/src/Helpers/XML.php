<?php

namespace Boctulus\ApiClient\Helpers;

class XML
{
    // antes isXML
    static function isValidXML(string $str, bool $fast_check = false) 
    {
        $str = trim($str);

        if ((substr($str, 0, 1) != '<') || substr($str, -1) !== '>'){
            return false;
        }

        if ($fast_check){
            return true;
        }

        // Habilitar el uso de errores internos de libxml
        libxml_use_internal_errors(true);
        
        // Intentar cargar el string XML
        $xml = simplexml_load_string($str);

        // Verificar si hubo errores al cargar el XML
        $is_xml = ($xml !== false);

        // Limpiar los errores de libxml
        libxml_clear_errors();

        return $is_xml;
    }

    /*
        The intend is to get the "DOM selector" given a text which should be found as substring of a text node

        Verificar si funciona!
    */
    static function getSelectorByText(string $html, string $text) : string {
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
            $selector = "{$nodeName}[{$nodeIndex}]{$selector}"; 

            $node = $node->parentNode;
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
        Devuelve array de los nodos (en XML/HTML) de los que coinciden con el selector

        Ej:

        // Extrae todo el codigo Javascript
        $js = XML::extractNodes($content, '//script');

        Si se especifica el atributo, obtiene solo el contenido de los atributos

        Ej:
        XML::extractNodes($content, '//script', 'src');

        Ej:

        $html = '
        <html>
            <body>
                <div class="content">Content 1</div>
                <div class="content">Content 2</div>
                <div class="footer">Footer</div>
            </body>
        </html>';

        dd(
            XML::extractNodes($html, "//div[@class='content']")
        );

        Resultado:

        Array
        (
            [0] => <div class="content">Content 1</div>
            [1] => <div class="content">Content 2</div>
        )
    */
    static function extractNodes(string $html, string $selector, string $attribute = null): array {
        $dom = static::getDocument($html);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query($selector);
    
        $result = array();
        foreach ($nodes as $node) {
            if ($attribute) {
                // Extrae el valor del atributo si está presente
                if ($node->hasAttribute($attribute)) {
                    $result[] = $node->getAttribute($attribute);
                }
            } else {
                // Extrae el contenido del nodo si no se proporciona atributo
                $result[] = $dom->saveXML($node);
            }
        }
    
        return $result;
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

    // Depredicar
    static function getNodesAsString(string $html, $selector, bool $as_string = true){
        $arr = XML::extractNodes($html, $selector);

        if (empty($arr)){
            return;
        }

        if ($as_string){
            $arr = implode("\r\n\r\n", $arr);
        }   

        return $arr;
    }
    
    static function saveXMLWithoutHeader(\DOMDocument $dom) : string {
        $str = $dom->saveXML();

        $str = Strings::afterIfContains($str, '<?xml version="1.0" standalone="yes"?>');
        $str = Strings::afterIfContains($str, '<?xml encoding="UTF-8"?>');
        $str = static::stripDOCTYPE($str);

        return ltrim($str);
    }

    /*
        @author Eaten by a Grue 
        https://stackoverflow.com/users/1767412/eaten-by-a-grue
    */
    static protected function DOMtoArray($root) {
        $result = array();
    
        if ($root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ($attrs as $attr) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }
    
        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if (in_array($child->nodeType,[XML_TEXT_NODE,XML_CDATA_SECTION_NODE])) {
                    $result['_value'] = $child->nodeValue;
                    return count($result) == 1
                        ? $result['_value']
                        : $result;
                }
    
            }
            $groups = array();
            foreach ($children as $child) {
                if (!isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = static::DOMtoArray($child);
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = array($result[$child->nodeName]);
                        $groups[$child->nodeName] = 1;
                    }
                    $result[$child->nodeName][] = static::DOMtoArray($child);
                }
            }
        }
        return $result;
    }

    /*
        @author Eaten by a Grue 
        https://stackoverflow.com/users/1767412/eaten-by-a-grue
    */
    static function toArray($xml) {
        $previous_value = libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        
        $dom->preserveWhiteSpace = false; 
        $dom->loadXml($xml);

        libxml_use_internal_errors($previous_value);
        
        if (libxml_get_errors()) {
            return [];
        }
        
        return static::DOMtoArray($dom);
    }
    
    /*
        Requiere del paquete de Composer spatie/array-to-xml

        composer require spatie/array-to-xml
    */
    static function fromArray(array $arr, string $root_elem = 'root', $header = true)
    {
        require_once __DIR__ . '/../../../vendor/composer/InstalledVersions.php';

        $class = 'InstalledVersions';

        if (!$class::isInstalled('spatie/array-to-xml')){
            throw new \Exception("Composer package spatie/array-to-xml is requiered");
        }

        $class = "\Spatie\ArrayToXml\ArrayToXml";

        if (!class_exists($class)){
            throw new \Exception("Class not found");
        } 

        $converter = new $class($arr, $root_elem);

        $result    = $converter::convert($arr, $root_elem, $header);

        if (!$header){
            $result = trim(substr($result, 21));
        }

        return $result;
    }

    /**
     * Load HTML content into a DOMDocument with optional flags.
     *
     * @param string $html The HTML content.
     * @param int $flags Optional flags for DOMDocument::loadHTML.
     * @return \DOMDocument The loaded DOMDocument.
     */
    public static function getDocument(string $html, $encoding = 'UTF-8', int $flags = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD): \DOMDocument
    {
        libxml_use_internal_errors(true);

        if ($encoding !== null){
            $dom = new \DOMDocument('1.0', $encoding);
            $dom->encoding = $encoding;

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
        } else {
            $dom = new \DOMDocument();
        }
      
        $dom->loadHTML($html, $flags);
        libxml_clear_errors();

        return $dom;
    }

    /**
     * Load HTML content into a DOMDocument with strict handling, including optional encoding declaration.
     *
     * @param string $html The HTML content.
     * @param int $flags Optional flags for DOMDocument::loadHTML.
     * @param string|null $encoding Optional encoding to declare at the start of the document.
     * @return \DOMDocument The loaded DOMDocument.
     */
    public static function getDocumentStrict(string $html, $encoding = 'UTF-8', int $flags = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD): \DOMDocument
    {       
        libxml_use_internal_errors(true);

        if ($encoding !== null){
            $dom = new \DOMDocument('1.0', $encoding);
            $dom->encoding = $encoding;

            $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);

            // Añadir la declaración XML si no existe y si se está trabajando con XHTML
            if (strpos($html, '<?xml') === false && stripos($html, '<!DOCTYPE html>') === false) {
                $html = '<?xml version="1.0" encoding="' . $encoding . '"?>' . $html;
            }
        } else {
            $dom = new \DOMDocument();
        }

        $dom->loadHTML($html, $flags);
        libxml_clear_errors();

        return $dom;
    }
    
    static function getXPath(string $html){
        return new \DOMXPath(
            static::getDocument($html)
        );
    }   

    /*
        Puede remover cualquier <tag>

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

        $html = static::saveXMLWithoutHeader($dom);
        
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
        $newPage = static::saveXMLWithoutHeader($dom);
        
        return $newPage;
    }

    /**
     * Removes HTML elements by tag name and specified attributes.
     *
     * This function searches for HTML elements by their tag name and a set of attributes,
     * then removes those elements from the HTML string if all specified attributes match.
     *
     * @param string $html The input HTML string.
     * @param string $tag The tag name of the elements to be removed (e.g., 'a', 'div').
     * @param array $attributes An associative array of attributes to match (e.g., ['data-link-type' => 'external']).
     * @return string The resulting HTML string with specified elements removed.
     *
     * @example
     *
     * $html = '<a data-link-type="external" data-type="linkpicker" href="http://example.com">Link</a>';
     * $attributes = [
     *     'data-link-type' => 'external',
     *     'data-type' => 'linkpicker'
     * ];
     * 
     * $newHtml = stripTagByAttribute($html, 'a', $attributes);
     * echo $newHtml; // Outputs: ''
     */
    static function stripTagByAttribute(string $html, string $tag, array $attributes): string {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
    
        $xpath = new \DOMXPath($dom);
    
        // Construir la expresión XPath para coincidir con los atributos especificados
        $conditions = [];
        foreach ($attributes as $name => $value) {
            $conditions[] = "@$name='$value'";
        }
        $conditionString = implode(' and ', $conditions);
    
        // Buscar elementos que coincidan con el tag y los atributos especificados
        $query = "//{$tag}[$conditionString]";
        $elements = $xpath->query($query);
    
        // Eliminar cada elemento encontrado
        foreach ($elements as $element) {
            $element->parentNode->removeChild($element);
        }
    
        // Obtener el HTML resultante como string
        $newHtml = $dom->saveHTML();
    
        return $newHtml;
    }    

    static function stripXMLTags(string $str): string {
        $str = Strings::afterIfContains($str, '<?xml version="1.0" standalone="yes"?>');
        $str = Strings::afterIfContains($str, '<?xml encoding="UTF-8"?>');

        return trim($str);
    }
    
    static function stripDOCTYPE(string $page) : string {
        $pattern = '/<!DOCTYPE[^>]+>/i';
        $stripped_page = preg_replace($pattern, '', $page);
        return $stripped_page;
    }


   
}


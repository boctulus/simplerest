<?php

namespace simplerest\core\libs;

use Symfony\Component\DomCrawler\Crawler;

/*
 * Class DomCrawler
 * 
 * La idea de esta clase es generar una interfaz simple y concistente 
 * sobre Symfony\Component\DomCrawler\Crawler
 * 
  
  Ej:
 
  <?php

    $html = file_get_contents('path/to/your/html/file.html');
    $crawler = new DomCrawler($html);

    // Verificar si existe un botón específico
    $buttonExists = $crawler->exists('button[name="AddToBasket"]');

    // Obtener todos los enlaces
    $links = $crawler->getAllLinks();

    // Obtener el texto de un elemento específico
    $text = $crawler->getText('h1.page-title');

    // Obtener el valor de un atributo
    $imageSrc = $crawler->getAttr('img.featured-image', 'src');

 */
class DomCrawler extends Crawler
{   
    /**
     * Obtiene los elementos que coinciden con el selector proporcionado.
     * 
     * @param string $selector El selector CSS utilizado para buscar elementos.
     * @return Crawler Un objeto Crawler que contiene los elementos encontrados.
     */
    function get(string $selector)
    {
        return $this->filter($selector);        
    }

    /**
     * Verifica si existen elementos que coinciden con el selector proporcionado.
     * 
     * Ejemplo:
     * ```php
     * $stock_status = $crawler->exists('button[name="AddToBasket"]') ? 'instock' : 'outofstock';
     * ```
     * 
     * @param string $selector El selector CSS utilizado para buscar elementos.
     * @return bool True si existen elementos que coinciden con el selector, false en caso contrario.
     */
    function exists(string $selector)
    {
        $el = $this->filter($selector);     
        return $el->count() > 0;   
    }

    /**
     * Obtiene el valor de un atributo de los elementos que coinciden con el selector proporcionado.
     * 
     * @param string $selector El selector CSS utilizado para buscar elementos.
     * @param string $attr_name El nombre del atributo cuyo valor se desea obtener.
     * @return string|null El valor del atributo o null si no se encontraron elementos o el atributo no existe.
     */
    function getAttr(string $selector, string $attr_name)
    {
        $node_list = $this->filter($selector);
        if ($node_list->count() === 0) {
            return null;
        }
        return $node_list->attr($attr_name);
    }

    /**
     * Obtiene el texto de los elementos que coinciden con el selector proporcionado.
     * 
     * @param string $selector El selector CSS utilizado para buscar elementos.
     * @return string|null El texto de los elementos o null si no se encontraron elementos.
     */
    function getText(string $selector)
    {        
        $node_list = $this->filter($selector);
        if ($node_list->count() === 0) {
            return null;
        }
        return $node_list->text();
    }

    /**
     * Obtiene el HTML de los elementos que coinciden con el selector proporcionado.
     * 
     * @param string $selector El selector CSS utilizado para buscar elementos.
     * @return string|null El HTML de los elementos o null si no se encontraron elementos.
     */
    function getHTML(string $selector)
    {
        $node_list = $this->filter($selector);
        if ($node_list->count() === 0) {
            return null;
        }
        return $node_list->html();
    }

     /**
     * Obtiene todos los valores de un atributo específico de los elementos seleccionados.
     *
     * @param string $selector
     * @param string $attr_name
     * @return array
     */
    function getAll(string $selector, string $attr_name = null): array
    {
        $results = [];
        $this->filter($selector)->each(function (Crawler $node) use (&$results, $attr_name) {
            if ($attr_name) {
                $results[] = $node->attr($attr_name);
            } else {
                $results[] = $node->text();
            }
        });

        return $results;
    }

    /**
     * Obtiene todos los enlaces (href) en el HTML.
     *
     * @return array
     */
    function getAllLinks(): array
    {
        return $this->getAll('a', 'href');
    }
}
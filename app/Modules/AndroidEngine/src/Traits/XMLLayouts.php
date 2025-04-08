<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\ErrorReporting;
use Boctulus\Simplerest\Modules\AndroidEngine\src\Traits\XML;

Trait XmlLayouts 
{
    use ErrorReporting;
    use XML;

    /**
     * Convierte XML a Markdown en un formato amigable y simple para layouts Android
     * 
     * @param string $xml Contenido XML
     * @param bool $include_ids Si es true, incluye los IDs entre corchetes
     * @return string Markdown formateado
     */
    function xmlToMarkdown(string $xml, bool $include_ids = false)
    {
        // Carga el XML como DOM
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suprimir errores XML
        $dom->loadXML($xml);
        libxml_clear_errors();

        $markdown = "# Layout XML\n\n";

        // Procesar el nodo raíz
        if ($dom->documentElement) {
            // Rastrea la ruta (breadcrumb) de componentes para reportes de error
            $pathStack = [];
            $markdown .= static::processDomNode($dom->documentElement, 0, $include_ids, $pathStack);
        }

        return $markdown;
    }

    
}
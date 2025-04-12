<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;
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

    /**
     * Genera una versión reutilizable de una vista XML como componente y su código de inclusión.
     * 
     * @param string $viewPath Ruta absoluta o relativa al archivo XML (relativa al rootPath si está definido).
     * @param string $viewId ID de la vista a extraer como componente (por ejemplo, "@id/keypad").
     * @param string $componentName Nombre del archivo del componente (sin .xml, por ejemplo, "keyboard_default").
     * @return array Array con 'component' (XML del componente), 'include' (código <include>), y 'replace' (identificador de la vista original).
     * @throws \Exception Si el archivo no existe, el XML es inválido, o la vista no se encuentra.
     */
    function generateReusableComponent(string $viewPath, string $viewId, string $componentName): array
    {
        // Normalizar el ID eliminando prefijos como @+id/ o @id/
        $cleanViewId = preg_replace('/^@(\+)?id\//', '', $viewId);

        // Determinar la ruta completa del archivo XML
        $rootPath = property_exists($this, 'rootPath') ? $this->rootPath : null;
        if ($rootPath && !preg_match('/^[A-Z]:\\\\/', $viewPath) && !preg_match('/^\//', $viewPath)) {
            $viewPath = rtrim($rootPath, '/\\') . DIRECTORY_SEPARATOR . ltrim($viewPath, '/\\');
        }

        // Verificar que el archivo exista
        if (!file_exists($viewPath)) {
            throw new \Exception("El archivo XML en '$viewPath' no existe.");
        }

        // Leer el contenido del archivo XML
        $xmlContent = file_get_contents($viewPath);
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suprimir errores de parseo para manejarlos manualmente
        $dom->loadXML($xmlContent);
        libxml_clear_errors();

        if (!$dom->documentElement) {
            throw new \Exception("El archivo XML en '$viewPath' es inválido o está vacío.");
        }

        // Buscar la vista con el ID especificado usando XPath
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('android', 'http://schemas.android.com/apk/res/android');
        $query = sprintf("//*[@android:id='@+id/%s' or @android:id='@id/%s']", $cleanViewId, $cleanViewId);
        $nodes = $xpath->query($query);

        if ($nodes->length === 0) {
            throw new \Exception("No se encontró una vista con el ID '$viewId' en '$viewPath'.");
        }

        $targetNode = $nodes->item(0);

        // Crear el documento XML para el componente reutilizable
        $componentDom = new \DOMDocument('1.0', 'utf-8');
        $componentDom->formatOutput = true;
        $importedNode = $componentDom->importNode($targetNode, true);
        $componentDom->appendChild($importedNode);

        // Copiar namespaces del nodo raíz original al componente
        $root = $dom->documentElement;
        foreach ($root->attributes as $attr) {
            if (strpos($attr->nodeName, 'xmlns:') === 0) {
                $importedNode->setAttribute($attr->nodeName, $attr->nodeValue);
            }
        }

        // Ajustar atributos para el componente reutilizable
        $importedNode->setAttribute('android:layout_width', 'match_parent');
        $importedNode->setAttribute('android:layout_height', 'match_parent');

        // Lista de atributos a eliminar para generalizar el componente
        $attributesToRemove = [
            'app:layout_constraintTop_toTopOf',
            'app:layout_constraintTop_toBottomOf',
            'app:layout_constraintBottom_toTopOf',
            'app:layout_constraintBottom_toBottomOf',
            'app:layout_constraintStart_toStartOf',
            'app:layout_constraintStart_toEndOf',
            'app:layout_constraintEnd_toStartOf',
            'app:layout_constraintEnd_toEndOf',
            'app:layout_constraintVertical_bias',
            'app:layout_constraintHorizontal_bias',
            'android:layout_margin',
            'android:layout_marginStart',
            'android:layout_marginEnd',
            'android:layout_marginTop',
            'android:layout_marginBottom',
            'android:layout_gravity',
            'android:layout_weight',
        ];

        foreach ($attributesToRemove as $attr) {
            if ($importedNode->hasAttribute($attr)) {
                $importedNode->removeAttribute($attr);
            }
        }

        // Generar el XML del componente
        $componentXml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $componentXml .= $componentDom->saveXML($componentDom->documentElement);

        // Generar el código <include> con los atributos de constraints originales
        $includeAttributes = [];
        foreach ($attributesToRemove as $attr) {
            if ($targetNode->hasAttribute($attr) && strpos($attr, 'app:layout_constraint') === 0) {
                $includeAttributes[] = sprintf('%s="%s"', $attr, $targetNode->getAttribute($attr));
            }
        }
        $includeXml = sprintf(
            '<include layout="@layout/%s" android:layout_width="0dp" android:layout_height="0dp" %s />',
            $componentName,
            implode(' ', $includeAttributes)
        );

        // Calcular las líneas aproximadas para el reemplazo
        $xmlLines = explode("\n", $xmlContent);
        $lineStart = $lineEnd = 1;
        $idPattern = sprintf('android:id="@(\+)?id\/%s"', preg_quote($cleanViewId, '/'));
        foreach ($xmlLines as $index => $line) {
            if (preg_match("/$idPattern/", $line)) {
                $lineStart = $index + 1;
                // Estimar el fin buscando el cierre del tag
                for ($i = $index; $i < count($xmlLines); $i++) {
                    if (strpos($xmlLines[$i], '</') !== false || strpos($xmlLines[$i], '/>') !== false) {
                        $lineEnd = $i + 1;
                        break;
                    }
                }
                break;
            }
        }

        $replace = sprintf('%s', basename($viewPath));

        // Devolver el resultado como un array
        return [
            'component' => $componentXml,
            'include' => $includeXml,
            'replace' => $replace,
        ];
    }
    
}
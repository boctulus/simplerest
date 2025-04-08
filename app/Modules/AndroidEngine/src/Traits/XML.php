<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;

Trait XML
{
    use ErrorReporting;

    /**
     * Procesa recursivamente un nodo DOM y lo convierte a Markdown
     * 
     * @param \DOMNode $node Nodo DOM a procesar
     * @param int $depth Nivel de profundidad actual para la indentación
     * @param bool $include_ids Si es true, incluye los IDs en la salida
     * @param array &$pathStack Pila para rastrear la ruta del elemento actual
     * @return string Markdown para este nodo y sus hijos
     */
    private function processDomNode($node, $depth = 0, $include_ids = false, &$pathStack = [])
    {
        $indent = str_repeat("  ", $depth);
        $markdown = "";

        // Solo procesar nodos de elemento
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return $markdown;
        }

        // Obtener el nombre del nodo (eliminar el namespace si existe)
        $nodeName = $node->localName ?: $node->nodeName;
        $nodeName = preg_replace('/^.*:/', '', $nodeName);

        // Añadir elemento a la pila de ruta
        $currentPosition = $nodeName . '[' . (count($pathStack) > 0 ? count($pathStack) : '0') . ']';
        $pathStack[] = $currentPosition;

        // Comenzar la sección del elemento
        $markdown .= "{$indent}- **{$nodeName}**";

        // Verificar si tiene ID
        $hasId = false;
        $id = null;

        // Buscar todos los atributos
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attrName = $attr->nodeName;
                $attrValue = $attr->nodeValue;

                // Verificar si es un atributo ID (con o sin namespace)
                if ($attrName === 'android:id' || $attrName === 'id' || substr($attrName, -3) === ':id') {
                    $hasId = true;
                    $id = static::cleanId($attrValue);
                    if ($include_ids) {
                        $markdown .= " [id:{$id}]";
                    }
                    break;
                }
            }
        }

        // Verificar si este elemento debería tener ID
        list($shouldHaveId, $severity) = static::shouldHaveId($nodeName);
        if ($shouldHaveId && !$hasId) {
            // Crear un breadcrumb para mostrar la ruta al elemento sin ID
            $breadcrumb = implode(' > ', $pathStack);
            static::addError("Elemento '{$nodeName}' sin ID encontrado en: {$breadcrumb}", $severity);
        }

        $markdown .= "\n";

        // Procesar nodos hijos
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $markdown .= static::processDomNode($child, $depth + 1, $include_ids, $pathStack);
                }
            }
        }

        // Quitar el elemento actual de la pila de ruta antes de regresar
        array_pop($pathStack);

        return $markdown;
    }

    /**
     * Lee un archivo XML y lo convierte a Markdown
     * 
     * @param string $file_path Ruta al archivo XML
     * @param bool $include_ids Si es true, incluye los IDs en el resultado
     * @return string Markdown formateado o mensaje de error
     */
    function markdown(string $file_path, bool $include_ids = false)
    {
        if (!file_exists($file_path)) {
            return "Error: El archivo `$file_path` no existe";
        }

        $xml = file_get_contents($file_path);
        if ($xml === false) {
            return "Error: No se pudo leer el archivo `$file_path`";
        }

        return static::xmlToMarkdown($xml, $include_ids);
    }

    /**
     * Lista los identificadores (@+id/{name}) en todas las vistas XML del proyecto
     * 
     * @return array Array estructurado con rutas y sus respectivos IDs
     * @throws \Exception Si no se encuentra el directorio de layouts
     */
    public function findAllXmlIds()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        // Posibles rutas para layouts
        $layoutPaths = [
            $this->rootPath . '/app/src/main/res/layout',
            $this->rootPath . '/app/src/main/res/layout-land',
            $this->rootPath . '/res/layout',
            $this->rootPath . '/res/layout-land'
        ];

        $result = [];
        $foundAnyPath = false;

        foreach ($layoutPaths as $layoutPath) {
            if (is_dir($layoutPath)) {
                $foundAnyPath = true;
                $files = glob($layoutPath . DIRECTORY_SEPARATOR . '*.xml');

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($content === false) continue;

                    $ids = [];
                    // Buscar todos los IDs en el formato @+id/name o @id/name
                    if (preg_match_all('/@\+?id\/([a-zA-Z0-9_]+)/', $content, $matches)) {
                        $ids = array_unique($matches[0]); // Eliminar duplicados
                    }

                    if (!empty($ids)) {
                        $relativePath = str_replace($this->rootPath, '', $file);
                        $result[$relativePath] = $ids;
                    }
                }
            }
        }

        if (!$foundAnyPath) {
            throw new \Exception("No se encontró ningún directorio de layouts");
        }

        return $result;
    }

    /**
     * Determina si un elemento debería tener un ID basado en su tipo
     * 
     * @param string $elementName Nombre del elemento
     * @return array [requiereId: bool, severidad: string]
     */
    private function shouldHaveId($elementName)
    {
        // Elementos que DEBEN tener ID (severidad WARNING)
        $mustHaveIdElements = [
            'Button',
            'ImageButton',
            'EditText',
            'TextView',
            'CheckBox',
            'RadioButton',
            'Switch',
            'ToggleButton',
            'Spinner',
            'SeekBar',
            'RatingBar',
            'SearchView',
            'RecyclerView',
            'ListView',
            'GridView',
            'ViewPager',
            'Fragment'
        ];

        // Elementos que DEBERÍAN tener ID (severidad INFO)
        $shouldHaveIdElements = [
            'LinearLayout',
            'RelativeLayout',
            'ConstraintLayout',
            'FrameLayout',
            'CoordinatorLayout',
            'DrawerLayout',
            'CardView',
            'ScrollView',
            'HorizontalScrollView',
            'TabLayout',
            'Toolbar',
            'ImageView'
        ];

        if (in_array($elementName, $mustHaveIdElements) || preg_match('/(Button|Input)$/', $elementName)) {
            return [true, static::SEVERITY_WARNING];
        } elseif (in_array($elementName, $shouldHaveIdElements) || preg_match('/(Layout|View)$/', $elementName)) {
            return [true, static::SEVERITY_INFO];
        }

        return [false, static::SEVERITY_INFO];
    }

}
<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;

trait Fragments
{
    private $rootPath;
    private $excludePaths = [];
    private $errors = [];

    use ErrorReporting;
    use Activities; // Trait para manejar Activities

    /**
     * Lista todos los fragmentos del proyecto
     * 
     * @return array Lista de fragmentos encontrados
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listFragments()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        // Usar el método de Files para buscar archivos .java y .kt recursivamente
        $sourcePath = $this->rootPath . '/app/src/main/java';

        $this->addError("Buscando archivos fuente en: {$sourcePath}", $this->getDebugLevel());

        // Verificar si el directorio existe
        if (!is_dir($sourcePath)) {
            $this->addError("El directorio de código fuente no existe: {$sourcePath}", $this->getErrorLevel());
            return [];
        }

        // Usar Files::recursiveGlob para buscar archivos .kt y .java
        $files = Files::recursiveGlob($sourcePath . DIRECTORY_SEPARATOR . '*.kt|*.java');

        if (empty($files)) {
            $this->addError("No se encontraron archivos .kt o .java en {$sourcePath}", $this->getWarningLevel());
            return [];
        }

        $this->addError("Encontrados " . count($files) . " archivos .kt y .java", $this->getDebugLevel());

        $fragments = [];

        // Analizar cada archivo para buscar Fragments
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                $this->addError("No se pudo leer el archivo: {$file}", $this->getWarningLevel());
                continue;
            }

            // Patrón para Fragments en Kotlin
            if (pathinfo($file, PATHINFO_EXTENSION) === 'kt') {
                // class MyFragment : Fragment() { ... }
                // class CustomFragment() : DialogFragment { ... }
                if (preg_match('/class\s+(\w+Fragment)\s*(\([^)]*\))?\s*:\s*\w*Fragment/i', $content, $matches)) {
                    $fragmentName = $matches[1];

                    // Obtener el nombre del paquete
                    $packageName = '';
                    if (preg_match('/package\s+([\w\.]+)/', $content, $packageMatches)) {
                        $packageName = $packageMatches[1];
                    }

                    $fragments[] = [
                        'name' => $fragmentName,
                        'package' => $packageName,
                        'path' => str_replace($this->rootPath, '', $file),
                        'fullName' => $packageName . '.' . $fragmentName
                    ];

                    $this->addError("Fragment encontrado: {$fragmentName} en {$file}", $this->getDebugLevel());
                }
            }
            // Patrón para Fragments en Java
            else if (pathinfo($file, PATHINFO_EXTENSION) === 'java') {
                // public class MyFragment extends Fragment { ... }
                if (preg_match('/class\s+(\w+Fragment)\s+extends\s+\w*Fragment/i', $content, $matches)) {
                    $fragmentName = $matches[1];

                    // Obtener el nombre del paquete
                    $packageName = '';
                    if (preg_match('/package\s+([\w\.]+);/', $content, $packageMatches)) {
                        $packageName = $packageMatches[1];
                    }

                    $fragments[] = [
                        'name' => $fragmentName,
                        'package' => $packageName,
                        'path' => str_replace($this->rootPath, '', $file),
                        'fullName' => $packageName . '.' . $fragmentName
                    ];

                    $this->addError("Fragment encontrado: {$fragmentName} en {$file}", $this->getDebugLevel());
                }
            }
        }

        if (empty($fragments)) {
            $this->addError("No se encontraron Fragments en ninguno de los archivos analizados", $this->getInfoLevel());
        }

        return $fragments;
    }

    /**
     * Lista todos los fragmentos del proyecto incluyendo referencias a ellos
     * 
     * @return array Lista de fragmentos con sus referencias
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listFragmentsWithReferences()
    {
        // Primero obtenemos todos los fragmentos
        $fragments = $this->listFragments();

        if (empty($fragments)) {
            return [];
        }

        // Para cada fragmento, buscamos referencias
        foreach ($fragments as &$fragment) {
            $references = [];

            // Verificamos si cada referencia no está vacía antes de agregarla
            $layoutRefs = $this->findFragmentInLayouts($fragment['name']);
            if (!empty($layoutRefs)) {
                $references['layouts'] = $layoutRefs;
            }

            $codeRefs = $this->findFragmentInCode($fragment['fullName'], $fragment['name']);
            if (!empty($codeRefs)) {
                $references['code'] = $codeRefs;
            }

            $navRefs = $this->findFragmentInNavigation($fragment['name']);
            if (!empty($navRefs)) {
                $references['navigation'] = $navRefs;
            }

            // Solo agregamos la clave 'references' si hay alguna referencia no vacía
            if (!empty($references)) {
                $fragment['references'] = $references;
            }
        }

        // Revisar fragmentos sin referencias y generar advertencias
        foreach ($fragments as $fragment) {
            if (!isset($fragment['references']) || empty($fragment['references'])) {
                $this->addError(
                    "El fragmento '{$fragment['name']}' no tiene referencias en el proyecto. " .
                        "Posible código no utilizado o fragmento sin implementar.",
                    $this->getWarningLevel()
                );
            }
        }

        return $fragments;
    }

    /**
     * Busca referencias al fragmento en archivos de layout XML
     * 
     * @param string $fragmentName Nombre del fragmento
     * @return array Referencias encontradas en archivos de layout
     */
    private function findFragmentInLayouts($fragmentName)
    {
        $references = [];

        $layoutPaths = [
            $this->rootPath . '/app/src/main/res/layout',
            $this->rootPath . '/app/src/main/res/layout-land',
            $this->rootPath . '/res/layout',
            $this->rootPath . '/res/layout-land'
        ];

        foreach ($layoutPaths as $layoutPath) {
            if (is_dir($layoutPath)) {
                $files = glob($layoutPath . DIRECTORY_SEPARATOR . '*.xml');

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($content === false) continue;

                    // Buscar el fragmento en el layout (como tag <fragment> o clase)
                    if (
                        preg_match('/<fragment[^>]*android:name="[^"]*' . preg_quote($fragmentName) . '"/', $content) ||
                        preg_match('/<fragment[^>]*class="[^"]*' . preg_quote($fragmentName) . '"/', $content) ||
                        preg_match('/<' . preg_quote($fragmentName) . '[^>]*>/', $content)
                    ) {

                        // Extraer el ID del fragmento si está disponible
                        $fragmentId = null;
                        if (preg_match('/android:id="@\+?id\/([^"]+)"/', $content, $idMatch)) {
                            $fragmentId = $idMatch[1];
                        }

                        $references[] = [
                            'source' => str_replace($this->rootPath, '', $file),
                            'type' => 'layout_inclusion',
                            'id' => $fragmentId,
                            'context' => 'Fragment declarado en layout XML'
                        ];
                    }
                }
            }
        }

        return $references;
    }

    /**
     * Busca referencias al fragmento en código fuente
     * 
     * @param string $fragmentFullName Nombre completo del fragmento (con paquete)
     * @param string $fragmentName Nombre simple del fragmento
     * @return array Referencias encontradas en código fuente
     */
    private function findFragmentInCode($fragmentFullName, $fragmentName)
    {
        $references = [];

        // Rutas donde buscar código fuente
        $sourcePaths = [
            $this->rootPath . '/app/src/main/java',
            $this->rootPath . '/src/main/java',
            $this->rootPath . '/java',
            $this->rootPath . '/app/src/main/kotlin',
            $this->rootPath . '/src/main/kotlin',
            $this->rootPath . '/kotlin'
        ];

        foreach ($sourcePaths as $sourcePath) {
            if (!is_dir($sourcePath)) continue;

            // Buscar recursivamente en archivos .java y .kt
            $javaFiles = Files::recursiveGlob($sourcePath . DIRECTORY_SEPARATOR . '*.java');
            $ktFiles   = Files::recursiveGlob($sourcePath . DIRECTORY_SEPARATOR . '*.kt');
            $files     = array_merge($javaFiles, $ktFiles);

            foreach ($files as $file) {
                // No analizar el propio archivo del fragmento
                if (strpos($file, $fragmentName . '.') !== false) continue;

                $content = file_get_contents($file);
                if ($content === false) continue;

                // Patrones comunes para buscar referencias a fragmentos
                $patterns = [
                    // Instanciación directa
                    '/new\s+' . preg_quote($fragmentName) . '\s*\(/',
                    // Referencia al fragmento por nombre completo
                    '/' . preg_quote($fragmentFullName) . '/',
                    // Transacciones de fragmentos
                    '/getSupportFragmentManager\(\).*?replace\(.*?' . preg_quote($fragmentName) . '/',
                    '/fragmentManager.*?replace\(.*?' . preg_quote($fragmentName) . '/',
                    '/getFragmentManager\(\).*?replace\(.*?' . preg_quote($fragmentName) . '/',
                    '/childFragmentManager.*?replace\(.*?' . preg_quote($fragmentName) . '/',
                    // Kotlin navigation component
                    '/findNavController\(\)\.navigate\(.*?' . preg_quote($fragmentName) . '/',
                    // Importaciones
                    '/import\s+' . preg_quote($fragmentFullName) . ';/'
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                        foreach ($matches[0] as $match) {
                            // Extraer el contexto (la línea donde se encuentra)
                            $matchPos = $match[1];
                            $lineStart = max(0, strrpos(substr($content, 0, $matchPos), "\n") + 1);
                            $lineEnd = strpos($content, "\n", $matchPos);
                            if ($lineEnd === false) $lineEnd = strlen($content);

                            $lineContent = trim(substr($content, $lineStart, $lineEnd - $lineStart));

                            // Determinar el tipo de referencia
                            $refType = 'unknown';
                            if (strpos($lineContent, 'import') !== false) {
                                $refType = 'import';
                            } elseif (strpos($lineContent, 'new') !== false) {
                                $refType = 'instantiation';
                            } elseif (
                                strpos($lineContent, 'replace') !== false ||
                                strpos($lineContent, 'add') !== false
                            ) {
                                $refType = 'transaction';
                            } elseif (strpos($lineContent, 'navigate') !== false) {
                                $refType = 'navigation';
                            }

                            // Evitar duplicados en el mismo archivo
                            $isDuplicate = false;
                            foreach ($references as $ref) {
                                if (
                                    $ref['source'] === str_replace($this->rootPath, '', $file) &&
                                    $ref['type'] === $refType
                                ) {
                                    $isDuplicate = true;
                                    break;
                                }
                            }

                            if (!$isDuplicate) {
                                $references[] = [
                                    'source' => str_replace($this->rootPath, '', $file),
                                    'type' => $refType,
                                    'context' => $lineContent
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $references;
    }

    /**
     * Busca referencias al fragmento en archivos de navegación
     * 
     * @param string $fragmentName Nombre del fragmento
     * @return array Referencias encontradas en archivos de navegación
     */
    private function findFragmentInNavigation($fragmentName)
    {
        $references = [];

        $navPaths = [
            $this->rootPath . '/app/src/main/res/navigation',
            $this->rootPath . '/res/navigation'
        ];

        foreach ($navPaths as $navPath) {
            if (is_dir($navPath)) {
                $files = glob($navPath . DIRECTORY_SEPARATOR . '*.xml');

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($content === false) continue;

                    // Buscar fragmento en archivos de navegación
                    if (preg_match('/<fragment[^>]*android:name="[^"]*' . preg_quote($fragmentName) . '"/', $content, $match)) {
                        // Extraer el ID del fragmento si está disponible
                        $fragmentId = null;
                        if (preg_match('/android:id="@\+?id\/([^"]+)"/', $content, $idMatch)) {
                            $fragmentId = $idMatch[1];
                        }

                        $references[] = [
                            'source' => str_replace($this->rootPath, '', $file),
                            'type' => 'navigation_graph',
                            'id' => $fragmentId,
                            'context' => $match[0]
                        ];
                    }

                    // Buscar acciones/destinos que apunten a este fragmento
                    if (preg_match_all('/<action[^>]*app:destination="@id\/([^"]+)"/', $content, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $destinationId = $match[1];

                            // Buscar si este destino corresponde al fragmento
                            if (preg_match('/<fragment[^>]*android:id="@\+?id\/' . preg_quote($destinationId) . '"[^>]*android:name="[^"]*' . preg_quote($fragmentName) . '"/', $content)) {
                                $references[] = [
                                    'source' => str_replace($this->rootPath, '', $file),
                                    'type' => 'navigation_action',
                                    'id' => $destinationId,
                                    'context' => 'Acción de navegación apunta a este fragmento'
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $references;
    }

    /**
     * Lista todas las Activities del proyecto incluyendo fragmentos enlazados --no parece funcionar [!]
     * 
     * @param bool $includeFragments Si true, incluye información sobre fragmentos enlazados
     * @return array Lista de Activities con información adicional
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listActivitiesWithFragments()
    {
        // Primero obtenemos todas las activities con sus referencias básicas
        $activities = $this->listActivitiesWithReferences();

        // Obtenemos todos los fragmentos con sus referencias
        $fragments = $this->listFragmentsWithReferences();

        // Mapeamos los fragmentos a las activities donde se usan
        foreach ($activities as &$activity) {
            $linkedFragments = [];

            foreach ($fragments as $fragment) {
                $isLinked = false;

                // Verificar si el fragmento está vinculado a esta actividad en código
                if (isset($fragment['references']['code'])) {
                    foreach ($fragment['references']['code'] as $codeRef) {
                        // Si la referencia proviene de un archivo que contiene el nombre de la actividad
                        if (strpos($codeRef['source'], $activity['name']) !== false) {
                            $isLinked = true;
                            break;
                        }
                    }
                }

                // Verificar si el fragmento está vinculado en layouts utilizados por esta actividad
                // (Esta es una aproximación, idealmente necesitaríamos analizar setContentView o inflateLayout)
                if (!$isLinked && isset($fragment['references']['layouts'])) {
                    // Buscar en el código de la actividad para ver qué layouts infla
                    $activityFile = $this->rootPath . $activity['path'];
                    if (file_exists($activityFile)) {
                        $activityContent = file_get_contents($activityFile);
                        if ($activityContent !== false) {
                            foreach ($fragment['references']['layouts'] as $layoutRef) {
                                $layoutName = basename($layoutRef['source'], '.xml');
                                // Buscar setContentView(R.layout.layoutName) o inflate(...R.layout.layoutName...)
                                if (
                                    preg_match('/setContentView\s*\(\s*R\.layout\.' . $layoutName . '\s*\)/', $activityContent) ||
                                    preg_match('/inflate\s*\([^)]*R\.layout\.' . $layoutName . '\s*[,)]/', $activityContent)
                                ) {
                                    $isLinked = true;
                                    break;
                                }
                            }
                        }
                    }
                }

                // Si el fragmento está vinculado a esta actividad, añadirlo a la lista
                if ($isLinked) {
                    $linkedFragments[] = [
                        'name' => $fragment['name'],
                        'fullName' => $fragment['fullName'],
                        'hasReferences' => isset($fragment['references'])
                    ];
                }
            }

            // Solo añadir la lista de fragmentos si hay alguno enlazado
            if (!empty($linkedFragments)) {
                $activity['linkedFragments'] = $linkedFragments;
            }
        }

        return $activities;
    }
}

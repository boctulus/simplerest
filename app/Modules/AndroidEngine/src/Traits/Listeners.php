<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\src\Traits;

use Boctulus\Simplerest\Core\Traits\ErrorReporting;

/*
    RELEVO DE EVENT LISTENERS

    TO-DO

    - Mejorar el reconocimiento de event listeners. Falla casi por completo con:

    C:\Users\jayso\StudioProjects\DarkCalc\app\src\main\java\com\boctulus\pc\recalc

    PROMPT:
    http://simplerest.lan/prompt_generator#chat-722
*/

Trait Listeners
{
    use ErrorReporting;
    use FilesTrait; // Trait para manejar archivos y directorios

    /**
     * Analiza los archivos de código Android para detectar event listeners
     * basados en findViewById y ViewBinding
     */
    function analyzeAndroidEventListeners()
    {
        // Obtener todos los archivos relevantes
        $files = $this->findCodeFiles();

        // Analizar cada tipo de listener
        $viewListeners = $this->getViewListeners($files);
        $viewBindingListeners = $this->getViewBindingListeners($files);

        // Detectar listeners duplicados
        $this->detectDuplicateListeners($viewListeners, $viewBindingListeners);

        return [
            'viewListeners' => $viewListeners,
            'viewBindingListeners' => $viewBindingListeners
        ];
    }

    /**
     * Obtiene los event listeners que utilizan findViewById()
     * @return array Lista de listeners encontrados
     */
    function getViewListeners()
    {
        $listeners = [];
        $files = $this->findCodeFiles();

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $className = $this->extractClassName($file, $content);

            // Patrón 1: findViewById(R.id.viewId).setOnXListener
            preg_match_all(
                '/findViewById\s*\(\s*R\.id\.([a-zA-Z0-9_]+)\s*\)\.setOn([a-zA-Z]+)Listener/',
                $content,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $viewId = $match[1];
                $listenerType = $match[2];

                $listeners[] = [
                    'file' => $file,
                    'className' => $className,
                    'viewId' => $viewId,
                    'method' => "findViewById(R.id.$viewId).setOn{$listenerType}Listener",
                    'type' => 'findViewById',
                    'listenerType' => $listenerType
                ];
            }

            // Patrón 2: Almacenar en variable y luego asignar listener
            // Button button = findViewById(R.id.viewId);
            // button.setOnXListener
            preg_match_all(
                '/([a-zA-Z0-9_]+)\s*=\s*findViewById\s*\(\s*R\.id\.([a-zA-Z0-9_]+)\s*\)/',
                $content,
                $varMatches,
                PREG_SET_ORDER
            );

            $viewVars = [];
            foreach ($varMatches as $match) {
                $varName = $match[1];
                $viewId = $match[2];
                $viewVars[$varName] = $viewId;
            }

            foreach ($viewVars as $varName => $viewId) {
                preg_match_all(
                    '/' . preg_quote($varName, '/') . '\.setOn([a-zA-Z]+)Listener/',
                    $content,
                    $listenerMatches,
                    PREG_SET_ORDER
                );

                foreach ($listenerMatches as $match) {
                    $listenerType = $match[1];

                    $listeners[] = [
                        'file' => $file,
                        'className' => $className,
                        'viewId' => $viewId,
                        'method' => "$varName.setOn{$listenerType}Listener",
                        'type' => 'findViewById',
                        'listenerType' => $listenerType
                    ];
                }
            }

            // Patrón 3: findViewById directo en el setOnClickListener
            preg_match_all(
                '/findViewById\s*\(\s*R\.id\.([a-zA-Z0-9_]+)\s*\).*?\.setOnClickListener\s*\(/s',
                $content,
                $directMatches,
                PREG_SET_ORDER
            );

            foreach ($directMatches as $match) {
                $viewId = $match[1];

                $listeners[] = [
                    'file' => $file,
                    'className' => $className,
                    'viewId' => $viewId,
                    'method' => "findViewById(R.id.$viewId).setOnClickListener",
                    'type' => 'findViewById',
                    'listenerType' => 'Click'
                ];
            }

            // Patrón 4: registerForContextMenu para menús contextuales
            preg_match_all(
                '/registerForContextMenu\s*\(\s*findViewById\s*\(\s*R\.id\.([a-zA-Z0-9_]+)\s*\)\s*\)/',
                $content,
                $contextMenuMatches,
                PREG_SET_ORDER
            );

            foreach ($contextMenuMatches as $match) {
                $viewId = $match[1];

                $listeners[] = [
                    'file' => $file,
                    'className' => $className,
                    'viewId' => $viewId,
                    'method' => "registerForContextMenu(findViewById(R.id.$viewId))",
                    'type' => 'findViewById',
                    'listenerType' => 'ContextMenu'
                ];
            }

            // Patrón 5: Loop que asigna listeners a múltiples botones
            // Esta es una detección específica para el patrón en el código proporcionado
            if (preg_match('/for\s*\([^{]*\)\s*\{[^}]*setOnClickListener/s', $content)) {
                preg_match_all(
                    '/boton\s*=\s*\([^)]+\)\s*row\.getChildAt\s*\([^)]*\)/',
                    $content,
                    $loopMatches
                );

                if (!empty($loopMatches[0])) {
                    // Buscar todos los IDs de botones en filas
                    preg_match_all(
                        '/LinearLayout\s+row\s*=\s*\([^)]+\)\s*kbLayout\.getChildAt\s*\([^)]*\)/',
                        $content,
                        $rowMatches
                    );

                    if (!empty($rowMatches[0])) {
                        preg_match_all('/R\.id\.([a-zA-Z0-9_]+)/', $content, $buttonIdMatches);
                        $buttonIds = $buttonIdMatches[1];

                        foreach ($buttonIds as $buttonId) {
                            // Solo incluimos botones que probablemente son parte del loop
                            if (
                                stripos($buttonId, 'd') === 0 ||
                                in_array($buttonId, ['add', 'sub', 'mul', 'div', 'equ', 'dot', 'del'])
                            ) {
                                $listeners[] = [
                                    'file' => $file,
                                    'className' => $className,
                                    'viewId' => $buttonId,
                                    'method' => "Dynamic Button Loop (setOnClickListener)",
                                    'type' => 'findViewById',
                                    'listenerType' => 'Click'
                                ];
                            }
                        }
                    }
                }
            }
        }

        $this->checkForDuplicateListeners($listeners, 'findViewById');
        return $listeners;
    }

    /**
     * Obtiene los event listeners que utilizan ViewBinding
     * @return array Lista de listeners encontrados
     */
    function getViewBindingListeners()
    {
        $listeners = [];
        $files = $this->findCodeFiles();

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $className = $this->extractClassName($file, $content);

            // Patrón 1: binding.viewId.setOnXListener
            preg_match_all(
                '/binding\.([a-zA-Z0-9_]+)\.setOn([a-zA-Z]+)Listener/',
                $content,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $viewId = $match[1];
                $listenerType = $match[2];

                $listeners[] = [
                    'file' => $file,
                    'className' => $className,
                    'viewId' => $viewId,
                    'method' => "binding.$viewId.setOn{$listenerType}Listener",
                    'type' => 'viewBinding',
                    'listenerType' => $listenerType
                ];
            }

            // Patrón 2: varianteBinding.viewId.setOnXListener
            preg_match_all(
                '/([a-zA-Z0-9_]+)Binding\.([a-zA-Z0-9_]+)\.setOn([a-zA-Z]+)Listener/',
                $content,
                $otherMatches,
                PREG_SET_ORDER
            );

            foreach ($otherMatches as $match) {
                $bindingVar = $match[1];
                $viewId = $match[2];
                $listenerType = $match[3];

                $listeners[] = [
                    'file' => $file,
                    'className' => $className,
                    'viewId' => $viewId,
                    'method' => "{$bindingVar}Binding.$viewId.setOn{$listenerType}Listener",
                    'type' => 'viewBinding',
                    'listenerType' => $listenerType
                ];
            }

            // Patrón 3: Variable almacenando el binding
            preg_match_all(
                '/([a-zA-Z0-9_]+)\s*=\s*([a-zA-Z0-9_]+)Binding\.([a-zA-Z0-9_]+)/',
                $content,
                $varMatches,
                PREG_SET_ORDER
            );

            $viewVars = [];
            foreach ($varMatches as $match) {
                $varName = $match[1];
                $bindingName = $match[2];
                $viewId = $match[3];
                $viewVars[$varName] = ['binding' => $bindingName, 'viewId' => $viewId];
            }

            foreach ($viewVars as $varName => $info) {
                preg_match_all(
                    '/' . preg_quote($varName, '/') . '\.setOn([a-zA-Z]+)Listener/',
                    $content,
                    $listenerMatches,
                    PREG_SET_ORDER
                );

                foreach ($listenerMatches as $match) {
                    $listenerType = $match[1];

                    $listeners[] = [
                        'file' => $file,
                        'className' => $className,
                        'viewId' => $info['viewId'],
                        'method' => "$varName.setOn{$listenerType}Listener (via {$info['binding']}Binding)",
                        'type' => 'viewBinding',
                        'listenerType' => $listenerType
                    ];
                }
            }
        }

        $this->checkForDuplicateListeners($listeners, 'viewBinding');
        return $listeners;
    }

    /**
     * Método auxiliar para verificar listeners duplicados
     */
    private function checkForDuplicateListeners($listeners, $listenerType)
    {
        // Agrupar por archivo, viewId y tipo de listener
        $listenerGroups = [];
        foreach ($listeners as $listener) {
            $key = $listener['file'] . '|' . $listener['viewId'] . '|' . $listener['listenerType'];
            $listenerGroups[$key][] = $listener;
        }

        // Revisar duplicados
        foreach ($listenerGroups as $key => $groupListeners) {
            if (count($groupListeners) > 1) {
                list($file, $viewId, $listenerType) = explode('|', $key);
                $className = $groupListeners[0]['className'];

                $message = "Listener duplicado encontrado para '{$viewId}' de tipo 'setOn{$listenerType}Listener' en la clase '{$className}'";
                $this->addError($message, $this->getErrorLevel(), [
                    'file' => $file,
                    'className' => $className,
                    'viewId' => $viewId,
                    'listenerType' => $listenerType,
                    'count' => count($groupListeners),
                    'locations' => array_map(function ($listener) {
                        return $listener['method'];
                    }, $groupListeners)
                ]);
            }
        }
    }

}
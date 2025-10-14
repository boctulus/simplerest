<?php

namespace Boctulus\Simplerest\Modules\AndroidEngine\Src\Traits;

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Traits\ErrorReporting;

Trait Activities
{
    use ErrorReporting;

      /**
     * Lista todas las Activities del proyecto
     * 
     * @return array Lista de Activities encontradas
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listActivities()
    {
        if ($this->rootPath === null) {
            throw new \Exception("Ruta raíz del proyecto no establecida. Use setRootPath() primero.");
        }

        // Usamos el método de Files para buscar archivos .java y .kt recursivamente
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

        $activities = [];

        // Analizar cada archivo para buscar Activities
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                $this->addError("No se pudo leer el archivo: {$file}", $this->getWarningLevel());
                continue;
            }

            // Patron para Activities en Kotlin
            if (pathinfo($file, PATHINFO_EXTENSION) === 'kt') {
                // class MainActivity : AppCompatActivity() { ... }
                // class UnlockActivity : AppCompatActivity { ... }
                // class SomeActivity() : AppCompatActivity { ... }
                if (preg_match('/class\s+(\w+Activity)\s*(\([^)]*\))?\s*:\s*\w*Activity/i', $content, $matches)) {
                    $activityName = $matches[1];

                    // Obtener el nombre del paquete
                    $packageName = '';
                    if (preg_match('/package\s+([\w\.]+)/', $content, $packageMatches)) {
                        $packageName = $packageMatches[1];
                    }

                    $activities[] = [
                        'name' => $activityName,
                        'package' => $packageName,
                        'path' => str_replace($this->rootPath, '', $file),
                        'fullName' => $packageName . '.' . $activityName
                    ];

                    $this->addError("Activity encontrada: {$activityName} en {$file}", $this->getDebugLevel());
                }
            }
            // Patrón para Activities en Java
            else if (pathinfo($file, PATHINFO_EXTENSION) === 'java') {
                // public class MainActivity extends AppCompatActivity { ... }
                if (preg_match('/class\s+(\w+Activity)\s+extends\s+\w*Activity/i', $content, $matches)) {
                    $activityName = $matches[1];

                    // Obtener el nombre del paquete
                    $packageName = '';
                    if (preg_match('/package\s+([\w\.]+);/', $content, $packageMatches)) {
                        $packageName = $packageMatches[1];
                    }

                    $activities[] = [
                        'name' => $activityName,
                        'package' => $packageName,
                        'path' => str_replace($this->rootPath, '', $file),
                        'fullName' => $packageName . '.' . $activityName
                    ];

                    $this->addError("Activity encontrada: {$activityName} en {$file}", $this->getDebugLevel());
                }
            }
        }

        if (empty($activities)) {
            $this->addError("No se encontraron Activities en ninguno de los archivos analizados", $this->getWarningLevel());
        }

        return $activities;
    }


    /**
     * Lista todas las Activities del proyecto incluyendo referencias a ellas
     * 
     * @return array Lista de Activities con sus referencias
     * @throws \Exception Si no se encuentra la carpeta de código Java/Kotlin
     */
    public function listActivitiesWithReferences()
    {
        // Primero obtenemos todas las activities
        $activities = $this->listActivities();

        if (empty($activities)) {
            return [];
        }

        // Para cada actividad, buscamos referencias
        foreach ($activities as &$activity) {
            $references = [];

            // Verificamos si cada referencia no está vacía antes de agregarla
            $manifest = $this->findActivityInManifest($activity['fullName']);
            if (!empty($manifest)) {
                $references['manifest'] = $manifest;
            }

            $menu = $this->findActivityInMenuFiles($activity['name']);
            if (!empty($menu)) {
                $references['menu'] = $menu;
            }

            $intents = $this->findActivityInIntents($activity['fullName']);
            if (!empty($intents)) {
                $references['intents'] = $intents;
            }

            // Solo agregamos la clave 'references' si hay alguna referencia no vacía
            if (!empty($references)) {
                $activity['references'] = $references;
            }
        }

        // Revisar actividades sin referencias y generar advertencias
        foreach ($activities as $activity) {
            if (!isset($activity['references']) || empty($activity['references'])) {
                $this->addError(
                    "La actividad '{$activity['name']}' no tiene referencias en el proyecto. " .
                        "Posible código no utilizado o actividad sin registrar.",
                    $this->getWarningLevel()
                );
            }
        }

        return $activities;
    }

    /**
     * Busca la Activity en el AndroidManifest.xml
     * 
     * @param string $activityFullName Nombre completo de la Activity (con paquete)
     * @return array Referencias encontradas en el manifest
     */
    private function findActivityInManifest($activityFullName)
    {
        $references = [];

        $manifestPaths = [
            $this->rootPath . '/app/src/main/AndroidManifest.xml',
            $this->rootPath . '/AndroidManifest.xml'
        ];

        foreach ($manifestPaths as $manifestPath) {
            if (file_exists($manifestPath)) {
                $content = file_get_contents($manifestPath);
                if ($content === false) continue;

                // Buscar la activity en el manifest (con o sin paquete completo)
                $activityName = substr($activityFullName, strrpos($activityFullName, '.') + 1);

                // Primero extraer todas las definiciones de activity para analizar cada una individualmente
                preg_match_all('/<activity[^>]*>(.*?)<\/activity>/s', $content, $activityBlocks);

                foreach ($activityBlocks[0] as $index => $activityBlock) {
                    // Verificar si esta actividad corresponde a la que estamos buscando
                    if (preg_match('/android:name="\.?' . preg_quote($activityName) . '"|android:name="' . preg_quote($activityFullName) . '"/', $activityBlock)) {
                        // Agregar referencia de declaración
                        preg_match('/<activity[^>]*android:name="[^"]*"[^>]*>/', $activityBlock, $activityTag);
                        if (!empty($activityTag[0])) {
                            $references[] = [
                                'source' => 'AndroidManifest.xml',
                                'type' => 'declaration',
                                'context' => trim($activityTag[0])
                            ];
                        }

                        // Verificar si es la actividad principal (MAIN y LAUNCHER)
                        if (
                            preg_match('/<action android:name="android.intent.action.MAIN"/', $activityBlock) &&
                            preg_match('/<category android:name="android.intent.category.LAUNCHER"/', $activityBlock)
                        ) {
                            $references[] = [
                                'source' => 'AndroidManifest.xml',
                                'type' => 'main_activity',
                                'context' => 'Esta es la actividad principal (MAIN/LAUNCHER)'
                            ];
                        }
                    }
                }

                break; // Solo necesitamos un manifest
            }
        }

        return $references;
    }

    /**
     * Busca la Activity en archivos de menú (XML)
     * 
     * @param string $activityName Nombre de la Activity
     * @return array Referencias encontradas en archivos de menú
     */
    private function findActivityInMenuFiles($activityName)
    {
        $references = [];

        $menuPaths = [
            $this->rootPath . '/app/src/main/res/menu',
            $this->rootPath . '/res/menu'
        ];

        foreach ($menuPaths as $menuPath) {
            if (is_dir($menuPath)) {
                $files = glob($menuPath . DIRECTORY_SEPARATOR . '*.xml');

                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if ($content === false) continue;

                    if (stripos($content, $activityName) !== false) {
                        $references[] = [
                            'source' => str_replace($this->rootPath, '', $file),
                            'type' => 'menu_reference',
                            'context' => 'Posible referencia en archivo de menú'
                        ];
                    }
                }
            }
        }

        return $references;
    }

    /**
     * Busca referencias a la Activity mediante Intents en código fuente
     * 
     * @param string $activityFullName Nombre completo de la Activity (con paquete)
     * @return array Referencias encontradas en código fuente
     */
    private function findActivityInIntents($activityFullName)
    {
        $references = [];
        $activityName = substr($activityFullName, strrpos($activityFullName, '.') + 1);

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
                $content = file_get_contents($file);
                if ($content === false) continue;

                // Patrones comunes para buscar intents hacia actividades
                $patterns = [
                    // Intent(this, ActivityName.class)
                    '/new\s+Intent\s*\(\s*.*?,\s*' . preg_quote($activityName) . '\.class\s*\)/',
                    // Intent(context, ActivityName::class.java)
                    '/new\s+Intent\s*\(\s*.*?,\s*' . preg_quote($activityName) . '::class\.java\s*\)/',
                    // Intent().setClass(this, ActivityName.class)
                    '/Intent\s*\(\s*\).*?setClass\s*\(\s*.*?,\s*' . preg_quote($activityName) . '\.class\s*\)/',
                    // Intent().setClass(this, ActivityName::class.java)
                    '/Intent\s*\(\s*\).*?setClass\s*\(\s*.*?,\s*' . preg_quote($activityName) . '::class\.java\s*\)/',
                    // startActivity<ActivityName>()
                    '/startActivity\s*<\s*' . preg_quote($activityName) . '\s*>\s*\(/'
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

                            $references[] = [
                                'source' => str_replace($this->rootPath, '', $file),
                                'type' => 'intent',
                                'context' => $lineContent
                            ];
                        }
                    }
                }
            }
        }

        return $references;
    }
}
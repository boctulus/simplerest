<?php

namespace Boctulus\Zippy\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Zippy\Libs\CategoryMapper;
use Boctulus\Zippy\Strategies\LLMMatchingStrategy;
use Boctulus\Zippy\Strategies\FuzzyMatchingStrategy;
use Boctulus\Simplerest\Core\Libs\Logger;

/**
 * Controlador para pruebas del nuevo sistema de mapeo de categorías
 */
class CategoryMappingTestController extends Controller
{
    /**
     * Test básico de mapping con estrategias configurables
     */
    public function test_mapping($raw = null)
    {
        $raw = $raw ?? 'Aceites Y Condimentos';
        
        echo "=== Test de Mapping con Estrategias ===\n\n";
        echo "Raw value: '{$raw}'\n\n";

        // Configurar CategoryMapper con LLM como estrategia principal
        CategoryMapper::configure([
            'strategies_order' => ['llm', 'fuzzy'],
            'llm_model' => 'qwen2.5:3b',
            'llm_verbose' => true,
            'thresholds' => [
                'llm' => 0.70,
                'fuzzy' => 0.40
            ]
        ]);

        // Mostrar información de estrategias
        $strategies = CategoryMapper::getStrategies();
        echo "✓ Estrategias disponibles:\n";
        foreach ($strategies as $name => $strategy) {
            $available = CategoryMapper::isStrategyAvailable($strategy) ? '✓ Disponible' : '✗ No disponible';
            $external = $strategy->requiresExternalService() ? ' (Servicio externo)' : ' (Local)';
            echo "  - {$name}: {$strategy->getName()}{$external} - {$available}\n";
        }
        echo "\n";

        // Probar resolución
        echo "--- Resolución automática (orden configurado) ---\n";
        $startTime = microtime(true);
        $result = CategoryMapper::resolve($raw, false); // No auto-save para test
        $endTime = microtime(true);
        
        $executionTime = round(($endTime - $startTime) * 1000, 2); // ms

        if (!empty($result)) {
            echo "✓ Categoría encontrada: " . implode(', ', $result) . "\n";
        } else {
            echo "✗ No se encontró categoría\n";
        }
        
        echo "Tiempo de ejecución: {$executionTime}ms\n\n";

        // Probar estrategias específicas
        $strategiesToTest = ['llm', 'fuzzy'];
        
        foreach ($strategiesToTest as $strategyName) {
            echo "--- Test específico: {$strategyName} ---\n";
            
            $startTime = microtime(true);
            $result = CategoryMapper::resolve($raw, false, $strategyName);
            $endTime = microtime(true);
            
            $time = round(($endTime - $startTime) * 1000, 2); // ms
            
            if (!empty($result)) {
                echo "✓ Categoría: " . implode(', ', $result) . " - {$time}ms\n";
            } else {
                echo "✗ Sin resultado - {$time}ms\n";
            }
            echo "\n";
        }
    }

    /**
     * Test de batch mapping
     */
    public function test_batch_mapping()
    {
        $testValues = [
            'Aceites Y Condimentos',
            'Lacteos y Derivados', 
            'Bebidas Sin Alcohol',
            'Galletitas y Golosinas',
            'Limpieza del Hogar',
            'Productos de Higiene Personal',
            'Carnes y Embutidos',
            'Verduras Frescas'
        ];

        echo "=== Test de Batch Mapping ===\n\n";

        // Configurar
        CategoryMapper::configure([
            'strategies_order' => ['llm', 'fuzzy'],
            'batch_size' => 2,
            'llm_model' => 'qwen2.5:3b',
            'llm_verbose' => false // Reducir verbosidad para batch
        ]);

        echo "Procesando " . count($testValues) . " valores con LLM + Fuzzy...\n\n";

        $startTime = microtime(true);
        $results = CategoryMapper::resolveBatch($testValues, false); // No auto-save
        $endTime = microtime(true);

        $executionTime = round($endTime - $startTime, 2);

        // Mostrar resultados
        $successful = 0;
        foreach ($results as $raw => $result) {
            echo "'{$raw}'\n";
            
            if (!empty($result)) {
                echo "  → " . implode(', ', $result) . "\n";
                $successful++;
            } else {
                echo "  → ✗ No clasificado\n";
            }
            echo "\n";
        }

        // Estadísticas
        $total = count($results);
        $successRate = $total > 0 ? round(($successful / $total) * 100, 2) : 0;
        
        echo "--- Estadísticas ---\n";
        echo "Tiempo total: {$executionTime}s\n";
        echo "Total: {$total}\n";
        echo "Exitosos: {$successful} ({$successRate}%)\n";
        echo "Promedio por item: " . round($executionTime / $total, 2) . "s\n";
    }

    /**
     * Comparación de estrategias
     */
    public function compare_strategies($raw = null)
    {
        $raw = $raw ?? 'Aceites Y Condimentos';
        
        echo "=== Comparación de Estrategias ===\n\n";
        echo "Raw value: '{$raw}'\n\n";

        // Configurar
        CategoryMapper::configure([
            'llm_model' => 'qwen2.5:3b',
            'llm_verbose' => false
        ]);

        $strategiesToTest = ['fuzzy', 'llm'];
        $results = [];
        
        foreach ($strategiesToTest as $strategy) {
            echo "--- {$strategy} ---\n";
            
            $startTime = microtime(true);
            $result = CategoryMapper::resolve($raw, false, $strategy);
            $endTime = microtime(true);
            
            $time = round(($endTime - $startTime) * 1000, 2); // ms
            
            $results[$strategy] = [
                'result' => $result,
                'time' => $time,
                'success' => !empty($result)
            ];
            
            if (!empty($result)) {
                echo "✓ Categoría: " . implode(', ', $result) . "\n";
            } else {
                echo "✗ Sin resultado\n";
            }
            echo "Tiempo: {$time}ms\n\n";
        }

        // Comparación final
        echo "--- Resumen Comparativo ---\n";
        foreach ($results as $strategy => $data) {
            $status = $data['success'] ? '✓' : '✗';
            echo "{$strategy}: {$status} {$data['time']}ms\n";
        }
        
        // Recomendación
        if ($results['llm']['success'] && $results['fuzzy']['success']) {
            $faster = $results['llm']['time'] < $results['fuzzy']['time'] ? 'LLM' : 'Fuzzy';
            echo "\nAmbas estrategias funcionan. {$faster} es más rápida.\n";
        } elseif ($results['llm']['success']) {
            echo "\nSolo LLM funcionó para esta categoría.\n";
        } elseif ($results['fuzzy']['success']) {
            echo "\nSolo Fuzzy funcionó para esta categoría.\n";
        } else {
            echo "\nNinguna estrategia funcionó para esta categoría.\n";
        }
    }

    /**
     * Test de configuración y disponibilidad
     */
    public function test_config()
    {
        echo "=== Test de Configuración ===\n\n";

        // Test configuración por defecto
        CategoryMapper::configure();
        $strategies = CategoryMapper::getStrategies();
        
        echo "Estrategias por defecto:\n";
        foreach ($strategies as $name => $strategy) {
            echo "  - {$name}: {$strategy->getName()}\n";
        }
        echo "\n";

        // Test configuración personalizada
        CategoryMapper::configure([
            'strategies_order' => ['llm', 'fuzzy'],
            'llm_model' => 'llama3.2',
            'llm_verbose' => true,
            'thresholds' => [
                'llm' => 0.80,
                'fuzzy' => 0.35
            ]
        ]);

        echo "Configuración personalizada aplicada.\n\n";

        // Test disponibilidad de LLM
        $llmStrategy = CategoryMapper::getStrategy('llm');
        if ($llmStrategy) {
            $available = CategoryMapper::isStrategyAvailable($llmStrategy);
            echo "LLM disponible: " . ($available ? 'Sí' : 'No') . "\n";
            
            if ($available) {
                $models = LLMMatchingStrategy::getAvailableModels();
                echo "Modelos disponibles: " . implode(', ', $models) . "\n";
            }
        }
        echo "\n";

        // Test registrar nueva estrategia
        echo "Registrando estrategia personalizada...\n";
        CategoryMapper::registerStrategy('custom_fuzzy', new FuzzyMatchingStrategy());
        
        $strategies = CategoryMapper::getStrategies();
        echo "Estrategias después de registrar:\n";
        foreach ($strategies as $name => $strategy) {
            echo "  - {$name}: {$strategy->getName()}\n";
        }
    }

    /**
     * Test de estadísticas
     */
    public function test_stats()
    {
        echo "=== Estadísticas del Sistema ===\n\n";

        $stats = CategoryMapper::getStats();
        
        echo "Mappings en base de datos:\n";
        echo "  - Total: {$stats['total']}\n";
        echo "  - Mapeados: {$stats['mapped']}\n";
        echo "  - No mapeados: {$stats['unmapped']}\n";
        echo "  - Revisados: {$stats['reviewed']}\n";
        echo "  - Necesitan revisión: {$stats['needs_review']}\n";
        echo "  - Tasa de mapeo: {$stats['mapping_rate']}%\n\n";

        // Test de categorías disponibles
        CategoryMapper::configure();
        $categories = CategoryMapper::getAvailableCategories();
        
        echo "Categorías disponibles: " . count($categories) . "\n";
        echo "Primeras 10:\n";
        $count = 0;
        foreach ($categories as $slug => $category) {
            if ($count >= 10) break;
            echo "  - {$slug}: {$category->name}\n";
            $count++;
        }
        
        if (count($categories) > 10) {
            echo "  ... y " . (count($categories) - 10) . " más.\n";
        }
    }

    /**
     * Test completo del sistema
     */
    public function test_full_system()
    {
        echo "=== Test Completo del Sistema ===\n\n";

        // Limpiar caché
        CategoryMapper::clearCache();
        echo "✓ Caché limpiado\n";

        // Configurar sistema
        CategoryMapper::configure([
            'strategies_order' => ['llm', 'fuzzy'],
            'llm_model' => 'qwen2.5:3b',
            'llm_verbose' => false,
            'batch_size' => 3,
            'thresholds' => [
                'llm' => 0.75,
                'fuzzy' => 0.40
            ]
        ]);
        echo "✓ Sistema configurado\n\n";

        // Test casos diversos
        $testCases = [
            'Aceites Y Condimentos' => 'almacen',
            'Lacteos y Derivados' => 'lacteos', 
            'Bebidas Sin Alcohol' => 'bebidas',
            'Producto Inexistente XYZ' => null,
            'lacteos' => 'lacteos', // Match directo
            'aceite' => 'almacen', // Keyword match
        ];

        echo "Probando casos diversos:\n\n";
        
        $totalTests = 0;
        $passedTests = 0;
        
        foreach ($testCases as $input => $expected) {
            $totalTests++;
            echo "Test: '{$input}'\n";
            
            $result = CategoryMapper::resolve($input, false);
            $actual = !empty($result) ? $result[0] : null;
            
            if ($expected === null) {
                // Esperamos que no haya resultado
                if (empty($result)) {
                    echo "  ✓ PASS - Sin resultado esperado\n";
                    $passedTests++;
                } else {
                    echo "  ✗ FAIL - Se esperaba sin resultado, obtuvo: " . implode(', ', $result) . "\n";
                }
            } else {
                // Esperamos un resultado específico
                if ($actual === $expected) {
                    echo "  ✓ PASS - Resultado: {$actual}\n";
                    $passedTests++;
                } else {
                    echo "  ✗ FAIL - Esperado: {$expected}, Obtuvo: " . ($actual ?? 'null') . "\n";
                }
            }
            echo "\n";
        }

        // Resumen final
        $passRate = round(($passedTests / $totalTests) * 100, 2);
        echo "--- Resumen Final ---\n";
        echo "Tests ejecutados: {$totalTests}\n";
        echo "Tests pasados: {$passedTests}\n";
        echo "Tasa de éxito: {$passRate}%\n";
        
        if ($passRate >= 80) {
            echo "🎉 Sistema funcionando correctamente!\n";
        } elseif ($passRate >= 60) {
            echo "⚠️ Sistema funciona con algunos problemas.\n";
        } else {
            echo "❌ Sistema necesita revisión.\n";
        }
    }
}

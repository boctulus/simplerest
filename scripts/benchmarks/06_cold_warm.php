<?php
/**
 * Benchmark 06: Cold vs Warm
 *
 * Mide diferencia entre primera ejecución (cold, sin caché) y ejecuciones
 * subsiguientes (warm, con OPcache en producción).
 *
 * NOTA: En CLI sin OPcache, cold vs warm es casi idéntico.
 *       En producción con PHP-FPM + OPcache la diferencia es DRÁSTICA.
 *
 * Run: php scripts/benchmarks/06_cold_warm.php
 */

$iterations = 10;

echo "=== Cold vs Warm Benchmark ===\n";
echo "OPcache: " . (extension_loaded('Zend OPcache') ? 'ENABLED' : 'NOT AVAILABLE') . "\n";
echo "Iterations: $iterations\n\n";

// Cold: first run in a fresh PHP process
echo "--- COLD (fresh process) ---\n";
$coldTimes = [];
for ($i = 0; $i < $iterations; $i++) {
    $start = microtime(true);
    // Execute a fresh PHP process that bootstraps the framework
    $output = shell_exec(PHP_BINARY . ' -r "
        \$s = hrtime(true);
        require_once \'' . __DIR__ . '/../../app.php\';
        echo (hrtime(true) - \$s) / 1e6;
    "');
    $coldTimes[] = (float) $output;
    printf("  Run %d: %.3f ms\n", $i + 1, $coldTimes[$i]);
}

printf("\nCold avg: %.3f ms\n\n", array_sum($coldTimes) / count($coldTimes));

// Warm: measure after framework is already loaded
echo "--- WARM (already loaded) ---\n";
require_once __DIR__ . '/../../app.php';
use Boctulus\Simplerest\Core\Libs\DB;
DB::getConnection('main');

$warmTimes = [];
for ($i = 0; $i < $iterations; $i++) {
    $s = hrtime(true);
    DB::select("SELECT * FROM perf_test WHERE id = 1");
    $warmTimes[] = (hrtime(true) - $s) / 1e6;
    printf("  Run %d: %.3f ms\n", $i + 1, $warmTimes[$i]);
}

printf("\nWarm avg: %.3f ms\n\n", array_sum($warmTimes) / count($warmTimes));

echo "--- Comparison ---\n";
printf("COLD avg: %.3f ms (bootstrap + framework load)\n", array_sum($coldTimes) / count($coldTimes));
printf("WARM avg: %.3f ms (only query + response)\n", array_sum($warmTimes) / count($warmTimes));
printf("Bootstrap overhead: ~%.3f ms\n",
    (array_sum($coldTimes) / count($coldTimes)) - (array_sum($warmTimes) / count($warmTimes)));

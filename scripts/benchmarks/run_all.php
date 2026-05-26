<?php
/**
 * Run all benchmarks in the suite.
 *
 * Usage:
 *   php scripts/benchmarks/run_all.php [iterations]
 *
 * Each benchmark can also be run independently:
 *   php scripts/benchmarks/00_bootstrap.php
 *   php scripts/benchmarks/01_internal.php
 *   etc.
 */

$iter = isset($argv[1]) ? (int) $argv[1] : 100;

$benchmarks = [
    '00_bootstrap.php'  => 'Bootstrap REAL (solo autoload+config+providers)',
    '01_internal.php'   => 'Internal overhead (componentes individuales)',
    '02_db_pure.php'    => 'Pure DB (PDO vs QB vs Model)',
    '03_http.php'       => 'HTTP completo (curl contra endpoints reales)',
    '04_concurrency.php'=> 'Concurrencia (curl_multi, throughput bajo carga)',
    '05_memory_classes.php' => 'Memoria + Clases cargadas',
    '06_cold_warm.php'  => 'Cold vs Warm (fresh process vs cached)',
];

echo "======================================\n";
echo "   SimpleRest Performance Benchmark Suite\n";
echo "======================================\n\n";

$baseDir = __DIR__;

foreach ($benchmarks as $script => $desc) {
    $path = "$baseDir/$script";
    if (!file_exists($path)) {
        echo "[SKIP] $script — not found\n\n";
        continue;
    }
    echo ">>> Running: $script ($desc)\n";
    echo str_repeat('=', 60) . "\n";
    ob_start();
    passthru(PHP_BINARY . " \"$path\"", $exitCode);
    $output = ob_get_clean();
    echo $output;
    echo str_repeat('=', 60) . "\n\n";
}

echo "All benchmarks completed.\n";

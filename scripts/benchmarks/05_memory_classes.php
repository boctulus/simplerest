<?php
/**
 * Benchmark 05: Memoria + Clases cargadas + Archivos incluidos
 *
 * Compara el footprint de diferentes enfoques.
 *
 * Run: php scripts/benchmarks/05_memory_classes.php
 */

require_once __DIR__ . '/../../app.php';

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Controllers\Api\PerfTest;
use Boctulus\Simplerest\Controllers\Api\PerfTestManual;
use Boctulus\Simplerest\Models\main\PerfTestModel;

DB::getConnection('main');

echo "=== Memory & Class Loading Benchmark ===\n\n";

$tests = [
    'Bare PHP (no framework)' => function() {
        $x = 1 + 1;
        return $x;
    },
    'DB::select() direct' => function() {
        return DB::select("SELECT * FROM perf_test WHERE id = 1");
    },
    'DB::table() query' => function() {
        return DB::table('perf_test')->where('id', 1)->get();
    },
    'Model::find(1)' => function() {
        return (new PerfTestModel(true))->find(1);
    },
    'ApiController->get(1)' => function() {
        // Won't work from CLI (auth), but we measure instantiation + call
        // return (new PerfTest())->get(1);
        return 'N/A (requires HTTP context)';
    },
    'ManualController->show(1)' => function() {
        return (new PerfTestManual())->show(1);
    },
];

$baseline_classes = count(get_declared_classes());
$baseline_files   = count(get_included_files());
$baseline_mem     = memory_get_usage(true);

foreach ($tests as $label => $fn) {
    $classes_before = count(get_declared_classes());
    $files_before   = count(get_included_files());
    $mem_before     = memory_get_usage(true);

    $fn();

    $classes_delta = count(get_declared_classes()) - $classes_before;
    $files_delta   = count(get_included_files()) - $files_before;
    $mem_delta     = memory_get_usage(true) - $mem_before;

    printf("%-30s classes=%-4d files=%-3d mem=%s\n",
        $label, $classes_delta, $files_delta, formatBytes($mem_delta));
}

echo "\n--- Baseline (after framework bootstrap) ---\n";
printf("Classes:  %d\n", count(get_declared_classes()));
printf("Files:    %d\n", count(get_included_files()));
printf("Memory:   %s\n", formatBytes(memory_get_usage(true)));
printf("Peak:     %s\n", formatBytes(memory_get_peak_usage(true)));

function formatBytes($b) {
    $units = ['B','KB','MB','GB'];
    $i = 0;
    while ($b >= 1024 && $i < 3) { $b /= 1024; $i++; }
    return round($b, 2) . ' ' . $units[$i];
}

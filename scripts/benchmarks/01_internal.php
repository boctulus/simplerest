<?php
/**
 * Benchmark 01: Internal PHP Overhead (expandido)
 *
 * Mide cada componente del framework por separado con 1000-100000 iteraciones.
 * Sin HTTP, sin bootstrap repetido.
 *
 * Run: php scripts/benchmarks/01_internal.php
 */

require_once __DIR__ . '/../../app.php';

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Controllers\Api\PerfTest;
use Boctulus\Simplerest\Controllers\Api\PerfTestManual;
use Boctulus\Simplerest\Models\main\PerfTestModel;
use Boctulus\Simplerest\Schemas\main\PerfTestSchema;
use Boctulus\Simplerest\Core\Libs\Validator;

DB::getConnection('main');

$config = [
    'Schema::get()'           => [100000, function() { PerfTestSchema::get(); }],
    'Model new'               => [10000,  function() { new PerfTestModel(true); }],
    'Model::where()->get()'   => [1000,   function() { (new PerfTestModel(true))->where('id', 1)->get(); }],
    'DB::select() direct'     => [1000,   function() { DB::select("SELECT * FROM perf_test WHERE id = 1"); }],
    'DB::select() LIST 10'    => [1000,   function() { DB::select("SELECT * FROM perf_test LIMIT 10"); }],
    'DB::insert()'            => [1000,   function() { DB::statement("INSERT INTO perf_test (name,email,age,status,salary,notes,created_at) VALUES ('x','y',1,'a',1,'z',NOW())"); }],
    'JSON encode 10 rows'     => [10000,  function() { json_encode(['data'=>[['id'=>1,'name'=>'x'],['id'=>2,'name'=>'y']]]); }],
    'JSON encode 1 row'       => [10000,  function() { json_encode(['data'=>['id'=>1,'name'=>'x']]); }],
    'Controller new (Auto)'   => [10000,  function() { new PerfTest(); }],
    'Controller new (Manual)' => [10000,  function() { new PerfTestManual(); }],
];

echo "=== Internal Overhead Benchmark ===\n";
echo str_repeat('-', 80) . "\n";
printf("%-30s %10s %12s %12s %12s %12s\n", 'Component', 'Iterations', 'Total (ms)', 'Avg (ms)', 'Min (ms)', 'Max (ms)');
echo str_repeat('-', 80) . "\n";

$results = [];
foreach ($config as $label => [$iterations, $fn]) {
    $times = [];
    $mem_before = memory_get_usage(true);
    $classes_before = count(get_declared_classes());

    for ($i = 0; $i < $iterations; $i++) {
        $s = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $s) / 1e6;
    }

    $mem_after = memory_get_usage(true);
    $classes_after = count(get_declared_classes());

    sort($times);
    $count = count($times);
    $total = array_sum($times);
    $avg = $total / $count;
    $min = $times[0];
    $max = $times[$count - 1];

    printf("%-30s %10d %12.3f %12.5f %12.3f %12.3f\n",
        $label, $iterations, $total, $avg, $min, $max);

    $results[] = compact('label','iterations','total','avg','min','max');
}

echo str_repeat('-', 80) . "\n";
echo "\n--- Memory & Class Count (after all tests) ---\n";
printf("Memory (real):      %s\n", formatBytes(memory_get_usage(true)));
printf("Peak memory (real): %s\n", formatBytes(memory_get_peak_usage(true)));
printf("Declared classes:   %d\n", count(get_declared_classes()));
printf("Included files:     %d\n", count(get_included_files()));

function formatBytes($b) {
    $units = ['B','KB','MB','GB'];
    $i = 0;
    while ($b >= 1024 && $i < 3) { $b /= 1024; $i++; }
    return round($b, 2) . ' ' . $units[$i];
}

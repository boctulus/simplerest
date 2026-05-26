<?php
/**
 * Benchmark 02: Pure DB (sin framework overhead)
 *
 * Mide solo la capa de base de datos: PDO directo vs DB::select() vs QueryBuilder vs Model.
 *
 * Run: php scripts/benchmarks/02_db_pure.php
 */

require_once __DIR__ . '/../../app.php';

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Models\main\PerfTestModel;

DB::getConnection('main');

$iterations = 1000;

echo "=== Pure DB Benchmark ===\n";
echo str_repeat('-', 80) . "\n";
printf("%-35s %10s %12s %12s\n", 'Method', 'Iterations', 'Avg (ms)', 'Total (ms)');
echo str_repeat('-', 80) . "\n";

$tests = [
    'PDO direct (raw query)' => function() {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=simplerest;charset=utf8', 'boctulus', 'gogogo#*$U&_441@#');
        $pdo->query("SELECT * FROM perf_test WHERE id = 1")->fetchAll(PDO::FETCH_ASSOC);
    },
    'DB::select()' => function() {
        DB::select("SELECT * FROM perf_test WHERE id = 1");
    },
    'DB::table()->where()->get()' => function() {
        DB::table('perf_test')->where('id', 1)->get();
    },
    'Model::where()->get()' => function() {
        (new PerfTestModel(true))->where('id', 1)->get();
    },
    'Model::find()' => function() {
        (new PerfTestModel(true))->find(1);
    },
];

foreach ($tests as $label => $fn) {
    if (str_contains($label, 'PDO')) {
        $iter = 100;
    } else {
        $iter = $iterations;
    }

    $times = [];
    for ($i = 0; $i < $iter; $i++) {
        $s = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $s) / 1e6;
    }

    $total = array_sum($times);
    $avg = $total / count($times);

    printf("%-35s %10d %12.5f %12.3f\n", $label, $iter, $avg, $total);
}

echo str_repeat('-', 80) . "\n";

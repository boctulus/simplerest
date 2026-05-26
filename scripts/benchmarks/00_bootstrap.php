<?php
/**
 * Benchmark 00: Bootstrap REAL
 *
 * Mide SOLO: autoload + config + providers + initialization
 * Cada iteración es un proceso PHP fresco (cold start real).
 *
 * Run: php scripts/benchmarks/00_bootstrap.php [iterations]
 */

$iterations = isset($argv[1]) ? (int) $argv[1] : 10;
$PHP_BIN    = defined('PHP_BINARY') ? PHP_BINARY : 'php';

echo "=== Bootstrap Benchmark ===\n";
echo "Iterations: $iterations\n";
echo "PHP: " . phpversion() . "\n";
echo "OPcache: " . (extension_loaded('Zend OPcache') ? 'YES' : 'NO') . "\n\n";

// Write measurement script to tmp
$tmpScript = __DIR__ . '/../tmp/_measure_boot.php';
file_put_contents($tmpScript, '<?php
$s = hrtime(true);
require_once \'' . __DIR__ . '/../../app.php\';
echo (hrtime(true) - $s) / 1e6;
');

echo "--- Cold bootstrap (fresh PHP process) ---\n";
$times = [];
for ($i = 0; $i < $iterations; $i++) {
    $output = shell_exec("\"$PHP_BIN\" \"$tmpScript\" 2>&1");
    $elapsed = (float) trim($output);
    $times[] = $elapsed;
    printf("  Run %2d: %.3f ms\n", $i + 1, $elapsed);
}

unlink($tmpScript);

sort($times);
$c = count($times);
$avg = array_sum($times) / $c;

echo "\n--- RESULTS ---\n";
printf("Bootstrap (avg):     %.3f ms\n", $avg);
printf("Bootstrap (min):     %.3f ms\n", $times[0]);
printf("Bootstrap (max):     %.3f ms\n", $times[$c - 1]);
printf("Bootstrap (p95):     %.3f ms\n", $times[(int)($c * 0.95)]);
printf("Bootstrap (median):  %.3f ms\n", $times[(int)($c * 0.50)]);

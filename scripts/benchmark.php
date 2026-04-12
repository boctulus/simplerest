<?php

/**
 * SimpleRest Bootstrap Benchmark
 *
 * Measures framework bootstrap time and basic performance metrics.
 * Run from project root: php scripts/benchmark.php
 *
 * Usage:
 *   php scripts/benchmark.php              # Run default (100 iterations)
 *   php scripts/benchmark.php 1000         # Run 1000 iterations
 */

$start_total = microtime(true);

$iterations = $argc > 1 ? (int)$argv[1] : 100;

echo "=== SimpleRest Bootstrap Benchmark ===\n";
echo "Iterations: $iterations\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "OPcache: " . (function_exists('opcache_get_status') && opcache_get_status() ? 'Enabled' : 'Disabled') . "\n";
echo str_repeat('-', 50) . "\n\n";

// --- Bootstrap Time ---
$bootstrap_times = [];

for ($i = 0; $i < $iterations; $i++) {
    $start = microtime(true);

    // Simulate framework bootstrap
    require_once __DIR__ . '/../vendor/autoload.php';

    // Force re-evaluation (clear static caches if any)
    $end = microtime(true);
    $bootstrap_times[] = ($end - $start) * 1000; // Convert to ms

    // Unset to simulate fresh bootstrap
    // Note: PHP doesn't truly unload classes, so this measures autoloader + init
}

$avg_bootstrap = array_sum($bootstrap_times) / count($bootstrap_times);
$min_bootstrap = min($bootstrap_times);
$max_bootstrap = max($bootstrap_times);

echo "📊 Bootstrap Time:\n";
echo sprintf("   Average: %.2f ms\n", $avg_bootstrap);
echo sprintf("   Min:     %.2f ms\n", $min_bootstrap);
echo sprintf("   Max:     %.2f ms\n", $max_bootstrap);
echo sprintf("   Total:   %.2f ms\n", array_sum($bootstrap_times));
echo "\n";

// --- Routing Performance ---
echo "🔀 Routing (compile + resolve):\n";
$routing_times = [];

for ($i = 0; $i < $iterations; $i++) {
    $start = microtime(true);

    try {
        $routes_file = __DIR__ . '/../config/routes.php';
        if (file_exists($routes_file)) {
            include_once $routes_file;
        }
    } catch (\Throwable $e) {
        // Ignore errors during routing
    }

    $end = microtime(true);
    $routing_times[] = ($end - $start) * 1000;
}

$avg_routing = array_sum($routing_times) / count($routing_times);
echo sprintf("   Average: %.2f ms\n", $avg_routing);
echo "\n";

// --- Config Load ---
echo "⚙️  Config Load:\n";
$config_times = [];

for ($i = 0; $i < $iterations; $i++) {
    $start = microtime(true);
    $config = include __DIR__ . '/../config/config.php';
    $end = microtime(true);
    $config_times[] = ($end - $start) * 1000;
}

$avg_config = array_sum($config_times) / count($config_times);
echo sprintf("   Average: %.2f ms\n", $avg_config);
echo "\n";

// --- Memory Usage ---
$memory_usage = memory_get_peak_usage(true);
$memory_real = memory_get_peak_usage(false);

echo "🧠 Memory Usage:\n";
echo sprintf("   Peak (allocated): %s\n", format_bytes($memory_usage));
echo sprintf("   Peak (real):      %s\n", format_bytes($memory_real));
echo "\n";

// --- Summary ---
$total_time = microtime(true) - $start_total;

echo str_repeat('=', 50) . "\n";
echo "📋 SUMMARY:\n";
echo str_repeat('-', 50) . "\n";
echo sprintf("   Bootstrap:  %.2f ms (avg)\n", $avg_bootstrap);
echo sprintf("   Routing:    %.2f ms (avg)\n", $avg_routing);
echo sprintf("   Config:     %.2f ms (avg)\n", $avg_config);
echo sprintf("   Memory:     %s (peak)\n", format_bytes($memory_usage));
echo sprintf("   Total time: %.2f s\n", $total_time);
echo str_repeat('=', 50) . "\n";

function format_bytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return sprintf('%.2f %s', $bytes, $units[$i]);
}

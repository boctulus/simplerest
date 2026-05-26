<?php
/**
 * Benchmark 04: Concurrencia real (curl_multi)
 *
 * Simula requests concurrentes usando curl_multi desde PHP.
 * Mide throughput, latencia bajo carga, y punto de saturación.
 *
 * NOTA: No reemplaza a ab/wrk/k6. Es un proxy cuando no están disponibles.
 *
 * Run: php scripts/benchmarks/04_concurrency.php [concurrency] [total_requests]
 */

$concurrency = isset($argv[1]) ? (int) $argv[1] : 10;
$total       = isset($argv[2]) ? (int) $argv[2] : 100;
$BASE        = 'http://simplerest.lan';

$endpoints = [
    "$BASE/api/v1/perf_test?limit=10",
    "$BASE/api/v1/perf_test/1",
    "$BASE/api/v1/perf_test_manual?page=1&pageSize=10",
    "$BASE/api/v1/perf_test_manual/1",
];

function multiRequest(array $urls, int $concurrency): array {
    $results = [];
    $batchSize = $concurrency;
    for ($i = 0; $i < count($urls); $i += $batchSize) {
        $batch = array_slice($urls, $i, $batchSize);
        $mh = curl_multi_init();
        $handles = [];
        $start = hrtime(true);

        foreach ($batch as $url) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10, CURLOPT_HEADER => false,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }

        $running = null;
        do { curl_multi_exec($mh, $running); curl_multi_select($mh); } while ($running > 0);

        $batchTime = (hrtime(true) - $start) / 1e6;

        foreach ($handles as $ch) {
            $results[] = [
                'time_ms' => $batchTime,
                'code'    => curl_getinfo($ch, CURLINFO_HTTP_CODE),
                'error'   => curl_error($ch),
            ];
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);
    }
    return $results;
}

echo "=== Concurrency Benchmark (curl_multi) ===\n";
echo "Concurrency: $concurrency | Total requests: $total\n";
echo str_repeat('-', 60) . "\n";

$targets = [];
for ($i = 0; $i < $total; $i++) {
    $targets[] = $endpoints[array_rand($endpoints)];
}

$start = hrtime(true);
$results = multiRequest($targets, $concurrency);
$wallTime = (hrtime(true) - $start) / 1e6;

$times   = array_column($results, 'time_ms');
$errors  = count(array_filter($results, fn($r) => !empty($r['error']) || $r['code'] >= 500));
$success = count($results) - $errors;
$rps     = $wallTime > 0 ? ($success / ($wallTime / 1000)) : 0;

echo "\n--- RESULTS ---\n";
printf("Total requests:      %d\n", count($results));
printf("Successful:          %d\n", $success);
printf("Errors:              %d\n", $errors);
printf("Wall time:           %.2f ms\n", $wallTime);
printf("Throughput (RPS):    %.1f\n", $rps);
printf("Avg latency:         %.2f ms\n", array_sum($times) / count($times));
printf("Avg request cost:    %.3f ms\n", $wallTime / count($times));
echo str_repeat('-', 60) . "\n";

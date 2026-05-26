<?php
/**
 * Benchmark 03: HTTP completo (refinado)
 *
 * Mide el ciclo HTTP completo con percentiles.
 * Reemplaza scripts/perf_benchmark.php
 *
 * Run: php scripts/benchmarks/03_http.php [iterations] [warmup]
 */

$iterations = isset($argv[1]) ? (int) $argv[1] : 100;
$warmup     = isset($argv[2]) ? (int) $argv[2] : 10;
$BASE       = 'http://simplerest.lan';

$scenarios = [
    ['LIST 10',     'GET',  "$BASE/api/v1/perf_test?limit=10",           null],
    ['SHOW by ID',   'GET',  "$BASE/api/v1/perf_test/1",                  null],
    ['CREATE',       'POST', "$BASE/api/v1/perf_test",                    ['name'=>'B','email'=>'b@t.com','age'=>1,'status'=>'a','salary'=>1,'notes'=>'x']],
    ['UPDATE',       'PUT',  "$BASE/api/v1/perf_test/1",                  ['name'=>'Updated']],
    ['DELETE',       'DELETE', "$BASE/api/v1/perf_test/10001",            null],
    ['LIST Manual', 'GET',  "$BASE/api/v1/perf_test_manual?page=1&pageSize=10", null],
    ['SHOW Manual',  'GET',  "$BASE/api/v1/perf_test_manual/1",           null],
];

function curlOnce(string $method, string $url, ?array $data = null): array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_CUSTOMREQUEST => $method,
    ]);
    if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $s = hrtime(true);
    $body = curl_exec($ch);
    $time = (hrtime(true) - $s) / 1e6;
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['time_ms' => $time, 'code' => $code, 'error' => $err, 'bytes' => strlen($body)];
}

echo "=== HTTP Benchmark ===\n";
echo "Server: $BASE\nIterations: $iterations | Warmup: $warmup\n";
echo str_repeat('-', 100) . "\n";
printf("%-15s %-10s %8s %8s %8s %8s %8s %8s %8s\n", 'Scenario', 'Method', 'Avg(ms)', 'Min', 'Max', 'p50', 'p95', 'p99', 'RPS');
echo str_repeat('-', 100) . "\n";

$allResults = [];
foreach ($scenarios as [$label, $method, $url, $data]) {
    for ($i = 0; $i < $warmup; $i++) { curlOnce($method, $url, $data); }

    $times = []; $errors = 0; $bytes = 0;
    for ($i = 0; $i < $iterations; $i++) {
        $r = curlOnce($method, $url, $data);
        $times[] = $r['time_ms'];
        if ($r['error']) $errors++;
        $bytes += $r['bytes'];
    }
    sort($times);
    $c = count($times);
    $avg = array_sum($times) / $c;
    printf("%-15s %-10s %8.2f %8.2f %8.2f %8.2f %8.2f %8.2f %8.1f\n",
        $label, $method, $avg, $times[0], $times[$c-1],
        $times[(int)($c*0.50)], $times[(int)($c*0.95)], $times[(int)($c*0.99)],
        ($c - $errors) / (array_sum($times) / 1000));

    $allResults[$label] = compact('avg','times','errors');
}

echo str_repeat('-', 100) . "\n";

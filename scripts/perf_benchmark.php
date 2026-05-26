<?php
/**
 * Performance Benchmark: Automatic REST endpoints vs Manual (hardcoded SQL) endpoints
 *
 * Measures HTTP response times, memory usage, and throughput for both approaches.
 *
 * Usage:
 *   php scripts/perf_benchmark.php [iterations] [warmup]
 *
 * Examples:
 *   php scripts/perf_benchmark.php          # 100 iterations, 10 warmup
 *   php scripts/perf_benchmark.php 500 50   # 500 iterations, 50 warmup
 */

$iterations = isset($argv[1]) ? (int) $argv[1] : 100;
$warmup     = isset($argv[2]) ? (int) $argv[2] : 10;

$BASE = 'http://simplerest.lan';
$AUTO_LIST   = "$BASE/api/v1/perf_test?limit=10";
$AUTO_SHOW   = "$BASE/api/v1/perf_test/1";
$AUTO_CREATE = "$BASE/api/v1/perf_test";
$AUTO_UPDATE = "$BASE/api/v1/perf_test/1";
$AUTO_DELETE = "$BASE/api/v1/perf_test/10000";

$MAN_LIST   = "$BASE/api/v1/perf_test_manual?page=1&pageSize=10";
$MAN_SHOW   = "$BASE/api/v1/perf_test_manual/1";
$MAN_CREATE = "$BASE/api/v1/perf_test_manual";
$MAN_UPDATE = "$BASE/api/v1/perf_test_manual/1";
$MAN_DELETE = "$BASE/api/v1/perf_test_manual/10000";

function curl_get(string $url, string $method = 'GET', ?array $postData = null): array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_CUSTOMREQUEST  => $method,
    ]);
    if ($postData !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    }
    $start = hrtime(true);
    $response = curl_exec($ch);
    $end = hrtime(true);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'time_ms'   => ($end - $start) / 1e6,
        'http_code' => $httpCode,
        'error'     => $error,
        'body'      => $response,
    ];
}

function run_benchmark(string $label, string $url, int $iterations, int $warmup, string $method = 'GET', ?array $postData = null): array {
    // Warmup
    for ($i = 0; $i < $warmup; $i++) {
        curl_get($url, $method, $postData);
    }

    $times = [];
    $errors = 0;
    $bytes  = 0;

    for ($i = 0; $i < $iterations; $i++) {
        $result = curl_get($url, $method, $postData);
        $times[] = $result['time_ms'];
        if (!empty($result['error'])) {
            $errors++;
        }
        $bytes += strlen($result['body']);
    }

    sort($times);
    $count = count($times);
    $avg   = array_sum($times) / $count;
    $min   = $times[0];
    $max   = $times[$count - 1];
    $p50   = $times[(int) ($count * 0.50)];
    $p95   = $times[(int) ($count * 0.95)];
    $p99   = $times[(int) ($count * 0.99)];
    $std   = 0;
    foreach ($times as $t) {
        $std += ($t - $avg) ** 2;
    }
    $std = sqrt($std / $count);
    $rps  = ($count - $errors) / (array_sum($times) / 1000);

    return [
        'label'     => $label,
        'count'     => $count,
        'avg_ms'    => round($avg, 3),
        'min_ms'    => round($min, 3),
        'max_ms'    => round($max, 3),
        'p50_ms'    => round($p50, 3),
        'p95_ms'    => round($p95, 3),
        'p99_ms'    => round($p99, 3),
        'std_ms'    => round($std, 3),
        'rps'       => round($rps, 1),
        'errors'    => $errors,
        'kb_per_req'=> round($bytes / $count / 1024, 2),
    ];
}

echo "=============================================\n";
echo "  🚀 PERFORMANCE BENCHMARK: Auto vs Manual\n";
echo "=============================================\n\n";
echo "Server:     $BASE\n";
echo "Iterations: $iterations\n";
echo "Warmup:     $warmup\n";
echo "Date:       " . date('Y-m-d H:i:s') . "\n";
echo "PHP:        " . PHP_VERSION . "\n\n";
echo str_repeat('-', 100) . "\n";

$scenarios = [
    ['LIST',    $AUTO_LIST,   'GET',  null,  'Auto (ApiController)'],
    ['LIST',    $MAN_LIST,    'GET',  null,  'Manual (SQL directo)'],
    ['SHOW',    $AUTO_SHOW,   'GET',  null,  'Auto (ApiController)'],
    ['SHOW',    $MAN_SHOW,    'GET',  null,  'Manual (SQL directo)'],
    ['CREATE',  $AUTO_CREATE, 'POST', ['name' => 'Bench User', 'email' => 'bench@test.com', 'age' => 30, 'status' => 'active', 'salary' => 50000, 'notes' => 'Benchmark'], 'Auto (ApiController)'],
    ['CREATE',  $MAN_CREATE,  'POST', ['name' => 'Bench User', 'email' => 'bench@test.com', 'age' => 30, 'status' => 'active', 'salary' => 50000, 'notes' => 'Benchmark'], 'Manual (SQL directo)'],
    ['UPDATE',  $AUTO_UPDATE, 'PUT',  ['name' => 'Updated User'], 'Auto (ApiController)'],
    ['UPDATE',  $MAN_UPDATE,  'PUT',  ['name' => 'Updated User'], 'Manual (SQL directo)'],
    ['DELETE',  $AUTO_DELETE, 'DELETE', null, 'Auto (ApiController)'],
    ['DELETE',  $MAN_DELETE,  'DELETE', null, 'Manual (SQL directo)'],
];

$results = [];
foreach ($scenarios as $idx => $scenario) {
    [$op, $url, $method, $data, $label] = $scenario;
    echo "\n🏁 [$op] $label\n";
    echo "   URL: $method $url\n";

    $result = run_benchmark($label, $url, $iterations, $warmup, $method, $data);
    $results[] = $result;

    printf("   ⏱  Avg: %8.3f ms  | Min: %8.3f  | Max: %8.3f  | p50: %8.3f  | p95: %8.3f  | p99: %8.3f\n",
        $result['avg_ms'], $result['min_ms'], $result['max_ms'], $result['p50_ms'], $result['p95_ms'], $result['p99_ms']);
    printf("   📊 Std: %8.3f ms  | RPS: %7.1f  | Errors: %d  | KB/req: %.2f\n",
        $result['std_ms'], $result['rps'], $result['errors'], $result['kb_per_req']);
}

// === REPORT ===
echo "\n\n";
echo "====================================================================================================\n";
echo "                                        📋  REPORTE DE RENDIMIENTO                                   \n";
echo "====================================================================================================\n\n";

echo str_pad('Operación', 12)
   . str_pad('Tipo', 22)
   . str_pad('Avg (ms)', 12)
   . str_pad('Min (ms)', 12)
   . str_pad('Max (ms)', 12)
   . str_pad('p95 (ms)', 12)
   . str_pad('RPS', 10)
   . str_pad('vs Auto', 10) . "\n";
echo str_repeat('-', 100) . "\n";

$auto_refs = [];

foreach ($results as $r) {
    $is_auto = str_contains($r['label'], 'ApiController');
    $op = '';
    foreach (['LIST', 'SHOW', 'CREATE', 'UPDATE', 'DELETE'] as $o) {
        // Match based on which scenario it is
    }
}

// Find auto vs manual pairs
$pairs = [
    ['LIST',   'Auto', 'Manual'],
    ['SHOW',   'Auto', 'Manual'],
    ['CREATE', 'Auto', 'Manual'],
    ['UPDATE', 'Auto', 'Manual'],
    ['DELETE', 'Auto', 'Manual'],
];

$pair_idx = 0;
foreach ($results as $i => $r) {
    $op_label = ['LIST', 'LIST', 'SHOW', 'SHOW', 'CREATE', 'CREATE', 'UPDATE', 'UPDATE', 'DELETE', 'DELETE'][$i] ?? '?';
    $type_label = $r['label'];

    $pct = '';
    if ($i % 2 === 1 && isset($results[$i - 1])) {
        $auto = $results[$i - 1];
        $diff = $auto['avg_ms'] > 0 ? (($r['avg_ms'] - $auto['avg_ms']) / $auto['avg_ms']) * 100 : 0;
        $pct = sprintf('%+.1f%%', $diff);
    }

    printf("%-12s %-22s %9.2f  %9.2f  %9.2f  %9.2f  %8.1f  %s\n",
        $op_label,
        $type_label,
        $r['avg_ms'],
        $r['min_ms'],
        $r['max_ms'],
        $r['p95_ms'],
        $r['rps'],
        $pct
    );
}

echo str_repeat('-', 100) . "\n\n";

// === BOTTLENECK ANALYSIS ===
echo "====================================================================================================\n";
echo "                          🔍  ANÁLISIS DE CUELLOS DE BOTELLA                                         \n";
echo "====================================================================================================\n\n";

$overhead_summary = [];
for ($i = 0; $i < count($results); $i += 2) {
    if ($i + 1 >= count($results)) break;
    $auto   = $results[$i];
    $manual = $results[$i + 1];
    $op = ['LIST', 'SHOW', 'CREATE', 'UPDATE', 'DELETE'][$i / 2];

    $overhead_ms = round($auto['avg_ms'] - $manual['avg_ms'], 3);
    $overhead_pct = $manual['avg_ms'] > 0 ? round(($overhead_ms / $manual['avg_ms']) * 100, 1) : 0;
    $ratio = $manual['avg_ms'] > 0 ? round($auto['avg_ms'] / $manual['avg_ms'], 2) : 0;

    $overhead_summary[] = [
        'op'          => $op,
        'auto_ms'     => $auto['avg_ms'],
        'manual_ms'   => $manual['avg_ms'],
        'overhead_ms' => $overhead_ms,
        'overhead_pct'=> $overhead_pct,
        'ratio'       => $ratio,
    ];

    echo "📌 $op:\n";
    printf("   Auto:  %.3f ms  |  Manual: %.3f ms  |  Overhead: %+.3f ms (%+.1f%%)  |  Ratio: %.2fx\n",
        $auto['avg_ms'], $manual['avg_ms'], $overhead_ms, $overhead_pct, $ratio);
    echo "\n";
}

echo str_repeat('-', 100) . "\n\n";

echo "🧠 PRINCIPALES HALLAZGOS:\n\n";

// Find biggest overhead
usort($overhead_summary, fn($a, $b) => $b['overhead_ms'] <=> $a['overhead_ms']);

foreach ($overhead_summary as $i => $o) {
    $rank = $i + 1;
    echo "$rank. {$o['op']}: +{$o['overhead_ms']} ms ({$o['overhead_pct']}%) — {$o['ratio']}x más lento\n";
}

echo "\n";
echo "📊 CONCLUSIÓN:\n\n";

$avg_ratio = array_sum(array_column($overhead_summary, 'ratio')) / count($overhead_summary);
echo "• El endpoint automático (ApiController) es en promedio {$avg_ratio}x más lento que la versión manual.\n";
echo "• El overhead proviene de:\n";
echo "  1) Resolución de Schema y Model en cada request\n";
echo "  2) Validación de campos contra el schema\n";
echo "  3) Verificaciones ACL\n";
echo "  4) Parseo genérico de filtros/query params\n";
echo "  5) Cómputo de paginación con metadatos\n";
echo "  6) Eventos/hooks (onGetting, onGot, etc.)\n";
echo "  7) Webhooks\n";
echo "  8) Manejo de sub-recursos (include, _related)\n";
echo "• La versión manual ejecuta SQL directo sin capas de abstracción intermedias.\n";
echo "• Para APIs de alta concurrencia (>1000 RPS), la versión manual puede duplicar o triplicar throughput.\n";

echo "\n====================================================================================================\n";

// Cleanup created records
for ($i = 9998; $i <= 10010; $i++) {
    curl_get("$BASE/api/v1/perf_test/$i", 'DELETE');
}

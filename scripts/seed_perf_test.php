<?php
/**
 * Seed perf_test table with 10K records for benchmarking.
 * Run: php scripts/seed_perf_test.php
 */

require_once __DIR__ . '/../app.php';

use Boctulus\Simplerest\Core\Libs\DB;

$start = microtime(true);

$names  = ['Alice','Bob','Carol','David','Eva','Frank','Grace','Henry','Iris','Jack',
           'Kate','Leo','Mia','Noah','Olive','Paul','Quinn','Ruth','Sam','Tina',
           'Uma','Victor','Wendy','Xander','Yara','Zack','Liam','Emma','Lucas','Ava'];
$statuses = ['active','inactive','pending'];

$total = 10000;
$data = [];
$batch = 0;

echo "Seeding $total records...\n";

for ($i = 0; $i < $total; $i++) {
    $name  = $names[array_rand($names)] . ' ' . $names[array_rand($names)] . rand(100, 999);
    $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
    $age   = rand(18, 80);
    $status = $statuses[array_rand($statuses)];
    $salary = round(30000 + rand(0, 2000) * 100, 2);
    $notes  = 'Record #' . ($i + 1) . ' - Lorem ipsum dolor sit amet.';

    $data[] = [
        'name'       => $name,
        'email'      => $email,
        'age'        => $age,
        'status'     => $status,
        'salary'     => $salary,
        'notes'      => $notes,
        'created_at' => date('Y-m-d H:i:s', strtotime("-" . rand(0, 365) . " days")),
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    if (count($data) >= 500) {
        DB::table('perf_test')->insert($data);
        $batch++;
        echo "\rBatch $batch inserted (" . ($batch * 500) . " records)";
        $data = [];
    }
}

if (!empty($data)) {
    DB::table('perf_test')->insert($data);
}

$elapsed = round(microtime(true) - $start, 2);
echo "\nDone! $total records seeded in {$elapsed}s\n";

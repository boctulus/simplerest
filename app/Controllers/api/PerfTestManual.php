<?php

namespace Boctulus\Simplerest\Controllers\Api;

use Boctulus\Simplerest\Core\Libs\DB;

class PerfTestManual extends \Boctulus\Simplerest\Core\Controllers\Controller
{
    function __construct()
    {
        parent::__construct();
        DB::getConnection('main');
    }

    public function index()
    {
        $page     = (int) ($_GET['page'] ?? 1);
        $pageSize = (int) ($_GET['pageSize'] ?? 10);
        $offset   = ($page - 1) * $pageSize;

        $rows = DB::select("SELECT * FROM perf_test LIMIT ? OFFSET ?", [$pageSize, $offset]);
        $total = DB::select("SELECT COUNT(*) as total FROM perf_test");
        $total = (int) ($total[0]['total'] ?? 0);

        return [
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'page_count' => (int) ceil($total / $pageSize),
        ];
    }

    public function show($id)
    {
        $rows = DB::select("SELECT * FROM perf_test WHERE id = ?", [(int) $id]);

        if (empty($rows)) {
            http_response_code(404);
            return ['error' => 'Not found'];
        }

        return ['data' => $rows[0]];
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        DB::statement(
            "INSERT INTO perf_test (name, email, age, status, salary, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $input['name'] ?? 'Unknown',
                $input['email'] ?? 'unknown@test.com',
                $input['age'] ?? 0,
                $input['status'] ?? 'active',
                $input['salary'] ?? 0,
                $input['notes'] ?? '',
            ]
        );

        $id = DB::getPdo()->lastInsertId();
        http_response_code(201);
        return ['data' => ['id' => $id]];
    }

    public function update($id)
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        DB::statement(
            "UPDATE perf_test SET name=?, email=?, age=?, status=?, salary=?, notes=?, updated_at=NOW() WHERE id=?",
            [
                $input['name'] ?? 'Unknown',
                $input['email'] ?? 'unknown@test.com',
                $input['age'] ?? 0,
                $input['status'] ?? 'active',
                $input['salary'] ?? 0,
                $input['notes'] ?? '',
                (int) $id,
            ]
        );

        $affected = DB::getPdo()->rowCount();
        return ['affected' => $affected];
    }

    public function destroy($id)
    {
        DB::statement("DELETE FROM perf_test WHERE id = ?", [(int) $id]);
        return ['affected' => DB::getPdo()->rowCount()];
    }
}

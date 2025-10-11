<?php
namespace Boctulus\DummyApi\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Request;

class ApiController extends Controller
{
    public function getTest() {
        return response()->json(['status' => 'ok', 'method' => 'GET']);
    }

    public function postTest(Request $req) {
        return response()->json([
            'status' => 'ok',
            'method' => 'POST',
            'body' => $req->all()
        ]);
    }

    public function headersTest(Request $req) {
        return response()->json([
            'status' => 'ok',
            'headers' => $req->headers()
        ]);
    }

    public function errorTest() {
        return response()->json(['status' => 'not found'], 404);
    }
}

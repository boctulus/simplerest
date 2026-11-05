<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;

final class EchoController extends Controller
{
    protected $output_format = 'json';

    public function index(): array
    {
        return ['controller' => 'echo', 'action' => 'index'];
    }

    public function show($id): array
    {
        return ['controller' => 'echo', 'id' => (int) $id];
    }

    public function boom(): array
    {
        throw new \RuntimeException('fixture failure');
    }
}

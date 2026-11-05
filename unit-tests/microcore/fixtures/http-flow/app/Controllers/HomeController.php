<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;

final class HomeController extends Controller
{
    protected $output_format = 'json';

    public function index(): array
    {
        return ['home' => true];
    }
}

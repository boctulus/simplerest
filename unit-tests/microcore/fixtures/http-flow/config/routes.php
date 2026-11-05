<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\WebRouter;

WebRouter::get('system/health', static fn (): array => ['ok' => true]);

WebRouter::get('web/echo/{id}', static fn ($id): array => ['id' => (int) $id]);

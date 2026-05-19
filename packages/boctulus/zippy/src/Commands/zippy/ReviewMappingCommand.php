<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyReviewMappingCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'review-mapping';
        $this->description = 'Revisa y corrige el mapeo de categorías';
        $this->aliases     = ['review'];
        $this->examples    = [
            'php com zippy review-mapping',
            'php com zippy review-mapping --category=electronics',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['category'],
            'flags'    => [],
            'options'  => [
                'category' => ['describe' => 'Categoría a revisar'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->review_mapping(...$this->toOpt($parsed));
    }
}

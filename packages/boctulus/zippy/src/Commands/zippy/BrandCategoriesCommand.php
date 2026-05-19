<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyBrandCategoriesCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'brand-categories';
        $this->description = 'Gestiona relaciones marca-categoría en Zippy';
        $this->aliases     = ['brand-cats'];
        $this->examples    = [
            'php com zippy brand-categories list',
            'php com zippy brand-categories sync',
        ];
    }

    public static function config(): array
    {
        return ['required' => [], 'optional' => [], 'flags' => [], 'options' => []];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->brand_categories($this->subcommand($parsed), ...$this->subOpts($parsed));
    }
}

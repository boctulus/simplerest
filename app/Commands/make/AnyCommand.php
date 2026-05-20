<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeAnyCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'any';
        $this->description = 'Genera múltiples artefactos a la vez (schema, model, api, etc.)';
        $this->aliases     = [];
        $this->examples    = [
            'php com make any baz -s -m -a -f',
            'php com make any tbl_contacto --schema --model --api --from=some_conn_id',
            'php com make any all --schema --model --force --from=main',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['from'],
            'flags'    => ['schema', 's', 'model', 'm', 'api', 'a', 'controller', 'c',
                           'console', 'provider', 'p', 'service', 'lib', 'l',
                           'force', 'f', 'unignore', 'u', 'sam', 'samf'],
            'options'  => [
                'from' => ['describe' => 'Conexión de base de datos origen'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed) ?? 'all';
        $this->any($name, ...$this->toOpt($parsed));
    }
}

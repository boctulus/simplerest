<?php

use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Traits\CommandTrait;
use Boctulus\Simplerest\libs\Documentor;

class DocCommand implements ICommand {
    use CommandTrait;

    /**
     * Método por defecto que procesa el comando "doc".
     *
     * @param string $path Ruta al archivo JSON.
     * @param mixed ...$opts Opciones adicionales (ej. --from_json).
     */
    public function doc($path, ...$opts) {
        $hasFromJson = false;
        foreach ($opts as $option) {
            if ($option === '--from_json') {
                $hasFromJson = true;
                break;
            }
        }

        if (!$hasFromJson) {
            $this->help();
            return;
        }

        // Llamar a la función de conversión a Markdown
        $result = Documentor::fromJSONFileToMarkDown($path);
        dd($result, 'MarkDown');
    }

    public function handle($args) {
        if (empty($args)) {
            $this->help();
            return;
        }

        // El primer argumento es la ruta al archivo JSON
        $path = array_shift($args);
        $hasFromJson = false;

        // Revisamos las opciones
        foreach ($args as $option) {
            if ($option === '--from_json') {
                $hasFromJson = true;
                break;
            }
        }

        if (!$hasFromJson) {
            $this->help();
            return;
        }

        $result = Documentor::fromJSONFileToMarkDown($path);
        dd($result, 'MarkDown');
    }

    function help($name = null, ...$args) {
        $str = <<<STR
		php com doc {json_file_path} --from_json

		Options:
		--from_json    Convert JSON file to Markdown using Documentor::fromJSONtoMarkDown()

		Examples:
		php com doc documentation_ej1.json --from_json
		php com doc /path/to/docs/api.json --from_json
		STR;

        dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
        dd($str);
    }
}

<?php

namespace Boctulus\LLMProviders\Controllers;

use Boctulus\LLMProviders\Factory\LLMFactory;
use Boctulus\Simplerest\Core\Controllers\Controller;

class OllamaController extends Controller
{
    public function ollama_prompt($prompt = null, $model = null)
    {
        [$cliPrompt, $cliModel] = $this->parseOllamaPromptArguments();

        $prompt = $cliPrompt ?? (is_string($prompt) && !str_starts_with($prompt, '--') ? $prompt : null);
        $model = $cliModel ?? (is_string($model) && !str_starts_with($model, '--') ? $model : null);
        $model = $model ?: (getenv('OLLAMA_MODEL') ?: 'ministral-3:3b');

        if ($prompt === null || trim($prompt) === '') {
            return 'Error: indicá --prompt="..." o el texto como primer argumento';
        }

        if (is_cli()) {
            fwrite(STDERR, "Consultando Ollama ({$model})...\n");
        }

        $llm = LLMFactory::ollama();

        $llm->setModel($model)
            ->addContent($prompt);

        $response = $llm->exec();

        if ($response['status'] === 200) {
            return $llm->getContent();
        }

        return 'Error: ' . ($llm->error() ?: 'Unknown error');
    }

    public function ollama_list()
    {
        $llm = LLMFactory::ollama();
        $models = $llm->listModels();
        
        if (is_cli()){
            print_r($models);
            return;
        }

        return $models;
    }

    private function parseOllamaPromptArguments(): array
    {
        $args = array_slice($_SERVER['argv'] ?? [], 3);
        $flags = [];
        $positional = [];

        for ($i = 0; $i < count($args); $i++) {
            $arg = (string) $args[$i];

            if (preg_match('/^--([a-z_]+)=(.*)$/i', $arg, $matches)) {
                $flags[strtolower($matches[1])] = $matches[2];
                continue;
            }

            if (preg_match('/^--([a-z_]+)$/i', $arg, $matches) && isset($args[$i + 1])) {
                $flags[strtolower($matches[1])] = (string) $args[$i + 1];
                $i++;
                continue;
            }

            $positional[] = $arg;
        }

        return [
            $flags['prompt'] ?? ($positional[0] ?? null),
            $flags['model'] ?? ($positional[1] ?? null),
        ];
    }
}

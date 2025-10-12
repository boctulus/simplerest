<?php

namespace Boctulus\LLMProviders\Controllers;

use Boctulus\LLMProviders\Factory\LLMFactory;
use Boctulus\Simplerest\Core\Controllers\Controller;

class LlmController extends Controller
{
    public function ollama_prompt($prompt = '¿Qué es PHP?', $model = 'qwen2.5:1.5b')
    {
        $llm = LLMFactory::ollama();

        $llm->setModel($model)
            ->addContent($prompt);

        $response = $llm->exec();

        if ($response['status'] == 200) {
            return $llm->getContent();
        } else {
            return "Error: " . ($llm->error() ?? "Unknown error");
        }
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
}

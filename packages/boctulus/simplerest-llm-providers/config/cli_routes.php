<?php

use Boctulus\Simplerest\Core\CliRouter;

// Main llm command to show help
CliRouter::command('llm', function() {
    echo "\n" . 'LLM Providers CLI Commands:' . "\n";
    echo '---------------------------' . "\n";
    echo 'php com llm ollama:list           - List all available Ollama models' . "\n";
    echo 'php com llm ollama:prompt --prompt="..." [--model=...] - Send a prompt to an Ollama model (default: ministral-3:3b)' . "\n";
    echo 'Env: OLLAMA_MODEL, OLLAMA_NUM_CTX, OLLAMA_NUM_PREDICT, OLLAMA_KEEP_ALIVE, OLLAMA_TIMEOUT' . "\n";
    echo "\n";
});

CliRouter::group('llm', function() {
    CliRouter::command('ollama:prompt', 'Boctulus\LLMProviders\Controllers\OllamaController@ollama_prompt');

    // php com llm ollama:list
    CliRouter::command('ollama:list', 'Boctulus\LLMProviders\Controllers\OllamaController@ollama_list');
});
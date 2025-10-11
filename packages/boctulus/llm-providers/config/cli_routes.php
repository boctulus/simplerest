<?php

use Boctulus\Simplerest\Core\CliRouter;

CliRouter::group('llm', function() {
    // Ollama
    CliRouter::command('ollama:prompt', 'Boctulus\LLMProviders\Controllers\LlmController@ollama_prompt');
    CliRouter::command('ollama:list', 'Boctulus\LLMProviders\Controllers\LlmController@ollama_list');
});
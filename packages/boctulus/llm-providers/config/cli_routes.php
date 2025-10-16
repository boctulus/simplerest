<?php

use Boctulus\Simplerest\Core\CliRouter;

CliRouter::group('llm', function() {
    CliRouter::command('ollama:prompt', 'Boctulus\LLMProviders\Controllers\LlmController@ollama_prompt');

    // php com llm ollama:list
    CliRouter::command('ollama:list', 'Boctulus\LLMProviders\Controllers\LlmController@ollama_list'); 
});
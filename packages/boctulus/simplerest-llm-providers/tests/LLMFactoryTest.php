<?php

declare(strict_types=1);

use Boctulus\LLMProviders\Exceptions\ProviderException;
use Boctulus\LLMProviders\Factory\LLMFactory;
use Boctulus\LLMProviders\Providers\ClaudeProvider;
use Boctulus\LLMProviders\Providers\GrokProvider;
use Boctulus\LLMProviders\Providers\OpenAIProvider;
use Boctulus\LLMProviders\Providers\OllamaProvider;
use PHPUnit\Framework\TestCase;

final class LLMFactoryTest extends TestCase
{
    public function testFactoryPublishesSupportedProviderAliases(): void
    {
        self::assertSame(
            ['openai', 'chatgpt', 'gpt', 'claude', 'anthropic', 'grok', 'xai', 'ollama'],
            LLMFactory::getSupportedProviders()
        );
        self::assertTrue(LLMFactory::isSupported(' OpenAI '));
        self::assertTrue(LLMFactory::isSupported('xai'));
        self::assertFalse(LLMFactory::isSupported('unknown'));
    }

    public function testFactoryRejectsUnknownProviderWithPackageException(): void
    {
        $this->expectException(ProviderException::class);

        LLMFactory::create('unknown');
    }

    public function testProvidersExposeCurrentDefaultsWithoutSendingRequests(): void
    {
        self::assertSame('claude-sonnet-4-6', (new ClaudeProvider('test-key'))->getModel());
        self::assertSame('grok-4.7', (new GrokProvider('test-key'))->getModel());
        self::assertSame('gpt-4o-mini', (new OpenAIProvider('test-key'))->getModel());
        self::assertInstanceOf(OllamaProvider::class, new OllamaProvider());
    }
}

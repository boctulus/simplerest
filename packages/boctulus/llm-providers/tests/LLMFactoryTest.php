<?php

declare(strict_types=1);

use Boctulus\LLMProviders\Exceptions\ProviderException;
use Boctulus\LLMProviders\Factory\LLMFactory;
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
}

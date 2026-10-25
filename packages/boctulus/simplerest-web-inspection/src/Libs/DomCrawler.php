<?php

namespace Boctulus\Simplerest\Integrations\Libs;

use Symfony\Component\DomCrawler\Crawler;

class DomCrawler extends Crawler
{
    public function get(string $selector)
    {
        return $this->filter($selector);
    }

    public function exists(string $selector): bool
    {
        return $this->filter($selector)->count() > 0;
    }

    public function getAttr(string $selector, string $attribute): ?string
    {
        $nodes = $this->filter($selector);
        return $nodes->count() === 0 ? null : $nodes->attr($attribute);
    }

    public function getText(string $selector): ?string
    {
        $nodes = $this->filter($selector);
        return $nodes->count() === 0 ? null : $nodes->text();
    }

    public function getHTML(string $selector): ?string
    {
        $nodes = $this->filter($selector);
        return $nodes->count() === 0 ? null : $nodes->html();
    }

    public function getAll(string $selector): array
    {
        $results = [];
        $this->filter($selector)->each(static function (Crawler $node) use (&$results): void {
            $results[] = $node;
        });

        return $results;
    }

    public function getAttributes(string $selector, string $attribute): array
    {
        $results = [];
        $this->filter($selector)->each(static function (Crawler $node) use (&$results, $attribute): void {
            $results[] = $node->attr($attribute);
        });

        return $results;
    }

    public function getTexts(string $selector, string $attribute): array
    {
        $results = [];
        $this->filter($selector)->each(static function (Crawler $node) use (&$results): void {
            $results[] = $node->text();
        });

        return $results;
    }

    public function getLinks(): array
    {
        return $this->getAttributes('a', 'href');
    }
}

<?php

namespace Boctulus\Zippy\Strategies;

use Boctulus\Zippy\Contracts\CategoryMatchingStrategyInterface;
use Boctulus\Simplerest\Core\Libs\Strings;

/**
 * Estrategia de matching basada en similitud de texto (fuzzy matching)
 *
 * Usa similar_text() de PHP para calcular porcentaje de similitud.
 * Es rápido pero no muy preciso para matching semántico.
 */
class FuzzyMatchingStrategy implements CategoryMatchingStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function match(string $raw, array $availableCategories, ?float $threshold = null): ?array
    {
        $threshold = $threshold ?? 0.40; // 40% por defecto
        $norm = Strings::normalize($raw);

        $best = null;
        $bestScore = 0;

        foreach ($availableCategories as $slug => $categoryData) {
            $name = is_array($categoryData) ? $categoryData['name'] : $categoryData->name;

            $nameNorm = Strings::normalize($name);
            $slugNorm = Strings::normalize($slug);

            // Probar con name y slug
            similar_text($norm, $nameNorm, $percName);
            similar_text($norm, $slugNorm, $percSlug);

            $perc = max($percName, $percSlug);

            if ($perc > $bestScore) {
                $bestScore = $perc;
                $best = $categoryData;
            }
        }

        // Convertir threshold de 0-1 a 0-100 si es necesario
        $thresholdPercent = ($threshold < 1) ? $threshold * 100 : $threshold;

        if ($bestScore >= $thresholdPercent) {
            return ['category' => $best, 'score' => $bestScore];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Fuzzy Text Similarity';
    }

    /**
     * {@inheritdoc}
     */
    public function requiresExternalService(): bool
    {
        return false;
    }
}

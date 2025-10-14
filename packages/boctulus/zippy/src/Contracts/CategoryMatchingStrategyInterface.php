<?php

namespace Boctulus\Zippy\Contracts;

/**
 * Interface para estrategias de matching de categorías
 *
 * Permite implementar diferentes algoritmos de matching (fuzzy, LLM, ML, etc.)
 * de forma intercambiable usando el patrón Strategy.
 */
interface CategoryMatchingStrategyInterface
{
    /**
     * Intenta hacer match de una categoría raw con las categorías disponibles
     *
     * @param string $raw Valor raw a matchear
     * @param array $availableCategories Categorías disponibles [slug => name]
     * @param float|null $threshold Umbral mínimo de confianza (opcional)
     * @return array|null ['category' => object|array, 'score' => float] o null si no hay match
     */
    public function match(string $raw, array $availableCategories, ?float $threshold = null): ?array;

    /**
     * Obtiene el nombre descriptivo de la estrategia
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Indica si la estrategia requiere conexión externa
     *
     * @return bool
     */
    public function requiresExternalService(): bool;
}

<?php

namespace Boctulus\Simplerest\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class ArbitrageCalculator
{
    public function calculateArbitrage(array $odds, float $stake = 100.0): array
    {
        // Validacion inicial
        foreach ($odds as $odd) {
            if ($odd['odd'] <= 0) {
                throw new \InvalidArgumentException('Invalid odd value.');
            }            
        }

        // Calcular valor de arbitraje
        $inverseSum = array_sum(array_map(function($odd) {
            return 1 / $odd['odd'];
        }, $odds));

        // Si es mayor a 1, no hay oportunidad
        if ($inverseSum >= 1) {
            return [
                'arbitrageValue' => $inverseSum,
                'profitPercentage' => -1,
                'proportions' => [],
                'stakes' => [],
                'returns' => []
            ];
        }

        // Calcular proporciones, stakes y retornos
        $proportions = [];
        $stakes = [];
        $returns = [];
        foreach ($odds as $odd) {
            $proportion = (1 / $odd['odd']) / $inverseSum;
            $proportions[] = $proportion;
            $currentStake = $stake * $proportion;
            $stakes[] = $currentStake;
            $returns[] = $currentStake * $odd['odd'];
        }

        // Calcular porcentaje de beneficio correctamente
        $totalReturn = $returns[0]; // Todos los retornos deberían ser iguales
        $profitPercentage = (($totalReturn / $stake) - 1) * 100;

        return [
            'arbitrageValue' => $inverseSum,
            'profitPercentage' => $profitPercentage,
            'proportions' => $proportions,
            'stakes' => $stakes,
            'returns' => $returns
        ];
    }
}
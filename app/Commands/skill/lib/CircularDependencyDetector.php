<?php

namespace Boctulus\Simplerest\Commands\Skill\Lib;

class CircularDependencyDetector
{
    const WHITE = 0;
    const GRAY  = 1;
    const BLACK = 2;

    public static function detectCircularDependencies(array $graph): array
    {
        $color        = [];
        $parent       = [];
        $cycles       = [];
        $visitedNodes = 0;

        foreach ($graph as $node => $deps) {
            $color[$node]  = self::WHITE;
            $parent[$node] = null;
        }

        foreach ($graph as $node => $deps) {
            if (($color[$node] ?? self::WHITE) === self::WHITE) {
                $visitedNodes += self::dfsVisit($node, $graph, $color, $parent, $cycles);
            }
        }

        return [
            'hasCycle'     => count($cycles) > 0,
            'cycles'       => $cycles,
            'visitedNodes' => $visitedNodes,
        ];
    }

    private static function dfsVisit(string $node, array &$graph, array &$color, array &$parent, array &$cycles): int
    {
        $visitedCount = 0;

        $color[$node] = self::GRAY;
        $visitedCount++;

        $dependencies = $graph[$node] ?? [];

        foreach ($dependencies as $dependency) {
            if (!array_key_exists($dependency, $graph)) {
                continue;
            }

            if (($color[$dependency] ?? self::WHITE) === self::GRAY) {
                $cycles[] = self::reconstructCycle($dependency, $node, $parent);
            } elseif (($color[$dependency] ?? self::WHITE) === self::WHITE) {
                $parent[$dependency] = $node;
                $visitedCount += self::dfsVisit($dependency, $graph, $color, $parent, $cycles);
            }
        }

        $color[$node] = self::BLACK;

        return $visitedCount;
    }

    private static function reconstructCycle(string $startNode, string $currentNode, array $parent): array
    {
        $cycle   = [$startNode];
        $current = $currentNode;

        while ($current !== $startNode && $current !== null) {
            array_unshift($cycle, $current);
            $current = $parent[$current] ?? null;
        }

        if ($cycle[0] === $startNode && $cycle[count($cycle) - 1] !== $startNode) {
            $cycle[] = $startNode;
        }

        return $cycle;
    }

    public static function hasCircularDependencies(array $graph): bool
    {
        $result = self::detectCircularDependencies($graph);
        return $result['hasCycle'];
    }

    public static function formatCycles(array $cycles): array
    {
        return array_map(function ($cycle) {
            return implode(' → ', $cycle);
        }, $cycles);
    }
}

<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '../../vendor/autoload.php';

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Libs\ArbitrageCalculator;

class ArbitrageCalculatorTest extends TestCase
{
    private $calculator;
    private $testData;

    protected function setUp(): void
    {
        $this->calculator = new ArbitrageCalculator();
        $this->testData = json_decode(file_get_contents(__DIR__ . '/arbitraje_data/data.json'), true);
    }

    public function primoTest()
    {
        // Datos exactos del ejemplo proporcionado
        $odds = [
            [
                'bookmaker' => 'Bet365.com',
                'type' => 'Over',
                'market' => 'Yellow Card O/U 9.5',
                'odd' => 1.85
            ],
            [
                'bookmaker' => 'Snai.it',
                'type' => 'Under',
                'market' => 'Yellow Card O/U 9.5',
                'odd' => 2.80
            ]
        ];

        $result = $this->calculator->calculateArbitrage($odds);

        // Verificar valor de arbitraje (debe ser 0.8976834)
        $this->assertEquals(0.8976834, round($result['arbitrageValue'], 7));

        // Verificar proporciones de apuesta
        $this->assertEquals(0.6021505354783212, round($result['proportions'][0], 16));
        $this->assertEquals(0.3978494645216788, round($result['proportions'][1], 16));

        // Verificar distribución para 100 euros
        $this->assertEquals(60.21505354783212, round($result['stakes'][0], 14));
        $this->assertEquals(39.78494645216788, round($result['stakes'][1], 14));

        // Verificar retornos esperados
        $this->assertEquals(111.3978490634894, round($result['returns'][0], 13));
        $this->assertEquals(111.3978500660701, round($result['returns'][1], 13));

        // Verificar porcentaje de beneficio
        $this->assertEquals(11.3, round($result['profitPercentage'], 1));
    }

    public function testCalculateArbitrage()
    {
        // Test caso Chelsea vs Liverpool
        $match = $this->testData['matches'][0];
        $result = $this->calculator->calculateArbitrage($match['odds']);
        
        $this->assertLessThan(1, $result['arbitrageValue']);
        $this->assertEquals(0.8976834, round($result['arbitrageValue'], 7));
        
        // Verificar distribución de apuestas para 100 euros
        $this->assertEquals(60.22, round($result['stakes'][0], 2));
        $this->assertEquals(39.78, round($result['stakes'][1], 2));
        
        // Verificar retornos esperados
        $this->assertEquals(111.40, round($result['returns'][0], 2));
        $this->assertEquals(111.40, round($result['returns'][1], 2));
        
        // Verificar porcentaje de beneficio con más detalle
        $totalReturn = $result['returns'][0];
        $expectedProfit = (($totalReturn / 100) - 1) * 100;
        $this->assertEquals(11.4, round($expectedProfit, 1), "Profit calculation error");
        $this->assertEquals(11.4, round($result['profitPercentage'], 1));
    }

    public function testNoArbitrageSituation()
    {
        // Test Barcelona vs Real Madrid (no debe dar oportunidad)
        $match = $this->testData['matches'][1];
        $result = $this->calculator->calculateArbitrage($match['odds']);
        
        $this->assertGreaterThan(1, $result['arbitrageValue']);
        $this->assertLessThan(0, $result['profitPercentage']);
    }
}

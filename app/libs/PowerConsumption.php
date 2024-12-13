<?php

namespace simplerest\libs;

use simplerest\core\libs\DB;
use simplerest\core\libs\Cli;
use simplerest\core\libs\Date;
use simplerest\core\libs\Strings;

class PowerConsumption {
    const MAX_CONSUMPTION =  300;
    const ANSI_RED = "\033[31m";
    const ANSI_YELLOW = "\033[33m";
    const ANSI_RESET = "\033[0m";


    static function first(){
        return DB::table('consumption')
        ->orderBy(['id' => 'asc'])
        ->value('reading');
    }

    static function last(){
        return DB::table('consumption')
        ->orderBy(['id' => 'desc'])
        ->value('reading');
    }

    static function getInitialReading() {
        $today = date('Y-m-d');
        $thirtyDaysAgo = Date::subDays($today, 30);
        
        // Primero intentamos obtener la lectura más antigua dentro de los últimos 30 días
        $reading = table('consumption')
            ->where(['created_at', $thirtyDaysAgo, '>='])
            ->orderBy(['created_at' => 'asc'])
            ->first();
            
        // Si no hay lecturas en los últimos 30 días, tomamos la primera lectura disponible
        if (!$reading) {
            $reading = table('consumption')
                ->orderBy(['created_at' => 'asc'])
                ->first();
        }
        
        return $reading;
    }

    static function getConsumptionData($currentReading = null): array {
        $today = date('Y-m-d');
        $first = static::getInitialReading();
        $first_day = $first['created_at'];
        
        $daysElapsed = Date::diffInDays($today, $first_day);
        $finalReading = $currentReading ?? self::last();
        
        $dailyConsumption = $finalReading - $first['reading'];
        $averageConsumption = $dailyConsumption / $daysElapsed;
        $excess = $dailyConsumption - self::MAX_CONSUMPTION;

        // Calculamos el consumo diario máximo permitido
        $maxDailyConsumption = self::MAX_CONSUMPTION / 30;
        
        // Calculamos el porcentaje actual respecto al máximo
        $currentPercentage = ($averageConsumption * 30 / self::MAX_CONSUMPTION) * 100;
        
        // Días restantes del mes
        $daysInMonth = date('t');
        $remainingDays = $daysInMonth - date('d');

        $message = 'OK. Dentro de los limites';
        if ($excess >= 0) {
            $message = Cli::ANSI_RED . 'Excedido!!!' . Cli::ANSI_RESET;
        } elseif ($remainingDays <= 7 && $currentPercentage >= 95) {
            $message = Cli::ANSI_YELLOW . 'Advertencia: Cerca del límite!' . Cli::ANSI_RESET;
        }

        return [
            'date' => $today,
            'consumption' => $dailyConsumption,
            'average_consumption' => round($averageConsumption, 2),
            'excess' => $excess,
            'message' => $message,
            'readings' => [
                'first' => [
                    'value' => $first['reading'],
                    'date' => $first['created_at']
                ],
                'last' => [
                    'value' => $finalReading,
                    'date' => $today
                ]
            ]
        ];
    }

    static function report(): array {
        return self::getConsumptionData();
    }

    static function calculate(int $currentReading, bool $save = false): array {
        $result = self::getConsumptionData($currentReading);
        
        if ($save) {
            self::save($currentReading);
        }

        return $result;
    }

    static function save(int $reading): void {
        $data = [
            'reading' => $reading,
            'created_at' => date('Y-m-d H:i:s')
        ];

        DB::getConnection();

        dd('Saving ...');
        $res = DB::table('consumption')->create($data);
    }
}
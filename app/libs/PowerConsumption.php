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
        return table('consumption')
        ->orderBy(['id' => 'asc'])
        ->value('reading');
    }

    static function last(){
        return table('consumption')
        ->orderBy(['id' => 'desc'])
        ->value('reading');
    }

    // Day (n-1)
    static function getPreviousReading($date) {
        return table('consumption')
            ->where(['created_at', $date, '<'])
            ->orderBy(['created_at' => 'desc'])
            ->first();
    }    

    static function getInitialReading() {
        $today = date('Y-m-d');
        $thirtyDaysAgo = Date::subDays($today, 30);
        
        // Primero intentamos obtener la lectura más antigua dentro de los últimos 30 días
        $reading = table('consumption') // Cambio table() por table()
            ->where(['created_at', $thirtyDaysAgo, '>='])
            ->orderBy(['created_at' => 'asc'])
            ->first();
                
        // Si no hay lecturas en los últimos 30 días, tomamos la primera lectura disponible
        if (!$reading) {
            $reading = table('consumption') // Cambio table() por table()
                ->orderBy(['created_at' => 'asc'])
                ->first();
        }
    
        if (!$reading) {
            throw new \Exception('No hay lecturas disponibles');
        }
    
        return $reading;
    }

    static function listReadings(int $days){
        return table('consumption')
        ->orderBy(['id' => 'desc'])
        ->limit($days)
        ->select([
            'created_at',
            'reading',
        ])
        ->get();
    }

    static function getConsumptionData($currentReading = null): array {
        $today = date('Y-m-d H:i:s');
        $first = static::getInitialReading();
        $first_day = $first['created_at'];
        $first_reading = $first['reading'];
        
        // Si no se proporciona currentReading, usar el último registro
        if ($currentReading === null) {
            $last = table('consumption')
                ->orderBy(['created_at' => 'desc'])
                ->first();
            $finalReading = $last['reading'];
            $finalDate = $last['created_at'];
            
            // Obtener el penúltimo registro como previous
            $previous = table('consumption')
                ->where(['created_at', $last['created_at'], '<'])
                ->orderBy(['created_at' => 'desc'])
                ->first();
        } else {
            // Si se proporciona currentReading, usar el último registro como previous
            $previous = table('consumption')
                ->orderBy(['created_at' => 'desc'])
                ->first();
            $finalReading = $currentReading;
            $finalDate = $today;
        }
        
        $daysElapsed = Date::diffInDays($finalDate, $first_day);
        $dailyConsumption = $finalReading - $first_reading;
        $averageConsumption = $dailyConsumption / ($daysElapsed ?: 1); // Evitar división por cero
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
    
        // Calcular días desde el inicio absoluto del período
        $absoluteFirst = table('consumption')
            ->orderBy(['created_at' => 'asc'])
            ->first();
        $daysFromStart = Date::diffInDays($finalDate, $absoluteFirst['created_at']);
        $periodDays = $daysFromStart % 30; // Días en el período actual de 30 días
    
        return [
            'date' => $finalDate,
            'consumption' => $dailyConsumption,
            'average_consumption' => round($averageConsumption, 2),
            'excess' => $excess,
            'message' => $message,
            'days_elapsed' => $periodDays,
            'readings' => [
                'first' => [
                    'value' => $first['reading'],
                    'date' => $first['created_at']
                ],
                'previous' => $previous ? [
                    'value' => $previous['reading'],
                    'date' => $previous['created_at']
                ] : null,
                'last' => [
                    'value' => $finalReading,
                    'date' => $finalDate
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
        $res = table('consumption')->create($data);
    }
}
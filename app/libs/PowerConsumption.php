<?php

namespace simplerest\libs;

use simplerest\core\libs\DB;
use simplerest\core\libs\Date;
use simplerest\core\libs\Strings;

class PowerConsumption {
    const MAX_CONSUMPTION =  300;


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

    static function report(){
        $today     = date('Y-m-d');
        $first     = table('consumption')->first();
        $first_day = $first['created_at'];

        $daysElapsed = Date::diffInDays($today, $first_day);

        $dailyConsumption   = self::last() - $first['reading'];
        $averageConsumption = $dailyConsumption / $daysElapsed;
        $excess             = $dailyConsumption - self::MAX_CONSUMPTION;

        return [
            'date' => $today,
            'consumption' => $dailyConsumption,
            'average_consumption' => round($averageConsumption, 2),
            'excess' => $excess,
        ];
    }

    static function calculate(int $currentReading, bool $save = false): array {
        $today     = date('Y-m-d'); 
        $first     = table('consumption')->first();
        $first_day = $first['created_at'];

        $daysElapsed = Date::diffInDays($today, $first_day);

        $dailyConsumption   = $currentReading - self::first();
        $averageConsumption = $dailyConsumption / $daysElapsed;
        $excess             = $dailyConsumption - self::MAX_CONSUMPTION;

        $result = [
            'date' => $today,
            'daily_consumption' => $dailyConsumption,
            'average_consumption' => round($averageConsumption, 2),
            'excess' => $excess,
        ];

        if ($save) {
            self::save($currentReading);
        }

        return $result;
    }

    static function save(int $reading): void {
        $data = [
            'reading' => $reading,
            'created_at' => date('Y-m-d H:i:s'), // Use created_at for timestamp
        ];

        DB::getConnection();

        dd('Saving ...');
        $res = DB::table('consumption')->create($data);
    }
}
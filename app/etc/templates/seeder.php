<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\GeneroFakerFactory;

class __MODEL_NAME__ extends Seeder
{
    static protected $qty = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\_MODEL_NAME__::insert($data);

        $seeder = new _MODEL_NAME__FakerFactory();
        for($i=0; $i<static::$qty; $i++){
            $row = $seeder->definition();
            DB::table('__TABLE_NAME__')->insert($row);
        }        
    }
}

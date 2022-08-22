<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\GeneroFakerFactory;

class __MODEL_NAME__Seeder extends Seeder
{
    static protected $qty = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeder = new _MODEL_NAME__Factory();
        for($i=0; $i<static::$qty; $i++){
            $row = $seeder->definition();
            DB::table('__TABLE_NAME__')->insertOrIgnore($row);
        }        
    }
}

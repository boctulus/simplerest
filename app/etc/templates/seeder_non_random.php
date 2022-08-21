<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class __MODEL_NAME__Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                __FIELDS__
            ],
            //...
        ];

        \App\Models\__MODEL_NAME__::insertOrIgnore($data);
    }
}


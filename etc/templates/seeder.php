<?php
namespace Database\Seeders;

use App\Models\__MODEL_NAME__;
use Illuminate\Database\Seeder;

class __MODEL_NAME__Seeder extends Seeder
{
   public function run()
   {
       $data = __DATA__;
       
       foreach($data as $item) {
           __MODEL_NAME__::create($item);
       }
   }
}
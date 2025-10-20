<?php
namespace Database\Seeders;

use Boctulus\FriendlyposWeb\Models\__MODEL_NAME__;
use Illuminate\Database\Seeder;

class __MODEL_NAME__Seeder extends Seeder
{
   public function run()
   {
       __MODEL_NAME__::factory()->count(10)->create();
   }
}
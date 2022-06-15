<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Entities\InterfaceApp;
use App\Entities\Company;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CreateInterfacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $interfaces = [
          'WEB P.O.S',
          'MOBILE PARKING',
          'WEB COMPANY',
          'MOBILE',
          'NOWHERE'
      ];
      $this->command->info("Insert interfaces...");
      foreach($interfaces as $interface){
          InterfaceApp::updateOrCreate(
            [
              'interface_name' => $interface
            ],
            [
              'interface_name' => $interface,
              'created_at' => Carbon::now(),
              'updated_at' => Carbon::now()
            ]
          );
      }
    }
}

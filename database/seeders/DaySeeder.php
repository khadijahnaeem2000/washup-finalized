<?php

namespace Database\Seeders;
use App\Models\Day;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    public function run()
    {
        $days = [
            
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'

         ];
         foreach ($days as $day) {
              Day::create(['name' => $day]);
         }
     }
}

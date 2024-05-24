<?php

namespace Database\Seeders;
use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{

    public function run()
    {
        $data = [
            'Low',
            'High',
            'Not Applicable',
         ];
         foreach ($data as $val) {
            Rating::create(['name' => $val]);
         }
     }
}

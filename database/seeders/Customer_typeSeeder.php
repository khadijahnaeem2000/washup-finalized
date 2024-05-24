<?php

namespace Database\Seeders;
use App\Models\Customer_type;
use Illuminate\Database\Seeder;

class Customer_typeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Retailers',
            'Commercials'
         ];
         foreach ($data as $val) {
            Customer_type::create(['name' => $val]);
         }
     }
}


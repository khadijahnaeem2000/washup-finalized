<?php

namespace Database\Seeders;
use App\Models\Reason;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{

    public function run()
    {
        $data = [
            'Missing Item',
            'Broken Tags',
            'Ironing Issue',
         ];
         foreach ($data as $val) {
            Reason::create(['name' => $val]);
         }
     }
}

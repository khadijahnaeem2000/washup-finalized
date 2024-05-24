<?php

namespace Database\Seeders;
use App\Models\Complaint_nature;
use Illuminate\Database\Seeder;

class Complaint_natureSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Washing',
            'Ironing',
            'Packing',
            'Logistic',
            'Lost items',
            'Customer Service'
         ];
         foreach ($data as $val) {
            Complaint_nature::create(['name' => $val]);
         }
     }
}

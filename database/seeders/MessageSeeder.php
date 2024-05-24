<?php

namespace Database\Seeders;
use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Billing',
            'Other',
            'Order Invoice',
            'Dropoff',
            'Payment',
            'Pickup',
            'Rider On Way'
         ];
         foreach ($data as $val) {
            Message::create(['name' => $val,
            'created_by' => 1,
            ]);
         }
     }
}

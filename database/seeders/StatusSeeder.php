<?php

namespace Database\Seeders;
use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{

    public function run()
    {
        $data = [
            'Pickup',
            'Drop off',
            'Pick & Drop',
            'Cancel',
            'Pending',
            'Dropped',
            'Complete', 
            'Inspection',
            'Ready for ride',
            'Content Verified',
            'Order Confirmation',
            'Picked up',
            'Delivered',
            'Moved to hub',
            'Moved to Wash-house',
            'Received to Hub',
            'Customer not available',
            'tags printed',
            'Special tags printed',
            'Polybag printed',
            'Special polybag printed',
         ];
         foreach ($data as $val) {
            Status::create(['name' => $val]);
         }
     }
}

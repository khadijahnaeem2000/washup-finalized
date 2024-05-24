<?php

namespace Database\Seeders;
use App\Models\Complaint_tag;
use Illuminate\Database\Seeder;

class Complaint_tagSeeder extends Seeder
{
    public function run()
    {
        $Washing = [
            'Stains',
            'Fading',
            'Tearing',
            'Smell',
            'Wash Quality',
            'Other',
            'Default'
         ];
         foreach ($Washing as $val) {
            Complaint_tag::create(['name' => $val,
                                      'complaint_nature_id' => 1]);
         }

         $Ironing = [
            'Burnt Marks',
            'Partial Ironing',
            'No Ironing',
            'Other',
            'Default'
         ];
         foreach ($Ironing as $val) {
            Complaint_tag::create(['name' => $val,
                                      'complaint_nature_id' => 2]);
         }

         $Packing = [
            'Instructions not followed',
            'Too many clothes',
            'Not sealed properly',
            'Other',
            'Default'
         ];
         foreach ($Packing as $val) {
            Complaint_tag::create(['name' => $val,
                                      'complaint_nature_id' => 3]);
         }

         $Logistic = [
            'Rider not on time',
            'Rider`s Behavior',
            'Slots always Full',
            'Other',
            'Default'
         ];
         foreach ($Logistic as $val) {
            Complaint_tag::create(['name' => $val,
                                      'complaint_nature_id' => 4]);
         }

         $Lost_items = [
            'Item Not Received',
            'Received Someone else`s item',
            'Wrong Order Received',
            'Partial Laundry Received',
            'Other',
            'Default'
         ];
         foreach ($Lost_items as $val) {
            Complaint_tag::create(['name' => $val,
                                      'complaint_nature_id' => 5]);
         }

         $Customer_Service = [
            'Default'
         ];
         foreach ($Customer_Service as $val) {
            Complaint_tag::create(['name' => $val,
                                      'complaint_nature_id' => 6]);
         }
     }
}

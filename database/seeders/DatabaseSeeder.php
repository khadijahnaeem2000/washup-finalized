<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            DaySeeder::class,
            UnitSeeder::class,
            MessageSeeder::class,
            StatusSeeder::class,
            Complaint_natureSeeder::class,
            Complaint_tagSeeder::class,
            ReasonSeeder::class,
            Customer_typeSeeder::class,
            RatingSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;
use App\Models\Customer_has_service;
use DB;
use Illuminate\Database\Seeder;

class OrderNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customer_has_services')->update(['order_number' => DB::raw('service_id')]);
    }
}

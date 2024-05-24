<?php

namespace Database\Seeders;
use App\Models\Order_has_service;
use Illuminate\Database\Seeder;
use DB;
class OrderHasServices extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('order_has_services')->update(['order_number' => DB::raw('service_id')]);
    }
}

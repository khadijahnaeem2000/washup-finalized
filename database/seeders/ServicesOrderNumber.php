<?php

namespace Database\Seeders;
use DB;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesOrderNumber extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('services')->update(['order_number' => DB::raw('id')]);
    }
}

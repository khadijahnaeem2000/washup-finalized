<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedSmallInteger('hub_id')->nullable();
            $table->boolean('hanger')->default(0);
            $table->float('weight', 8, 3)->nullable();
            $table->time('travel_time')->nullable();
            $table->time('time_at_loc')->nullable();
            $table->unsignedTinyInteger('status_id')->nullable();
            $table->unsignedSmallInteger('timeslot_id')->nullable();
            $table->unsignedSmallInteger('address_id')->nullable();
            $table->unsignedSmallInteger('rider_id')->nullable();
            $table->unsignedSmallInteger('area_id')->nullable();
            $table->unsignedSmallInteger('zone_id')->nullable();
            $table->unsignedTinyInteger('route')->nullable();
            $table->unsignedSmallInteger('created_by')->default(0);
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_plans');
    }
}

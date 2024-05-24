<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubHasTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hub_has_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id');
            $table->foreign('hub_id')->references('id')->on('distribution_hubs');
            $table->foreignId('time_slot_id');
            $table->foreign('time_slot_id')->references('id')->on('time_slots');
            $table->integer('location');
            
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
        Schema::dropIfExists('hub_has_time_slots');
    }
}

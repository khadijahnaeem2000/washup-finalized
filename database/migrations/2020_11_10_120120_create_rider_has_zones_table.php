<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderHasZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_has_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id');
            $table->foreign('rider_id')->references('id')->on('riders');
            $table->foreignId('zone_id');
            $table->foreign('zone_id')->references('id')->on('zones');
            $table->integer('priority')->nullable();
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
        Schema::dropIfExists('rider_has_zones');
    }
}

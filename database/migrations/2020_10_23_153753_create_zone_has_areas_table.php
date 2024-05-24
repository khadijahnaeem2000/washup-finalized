<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneHasAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_has_areas', function (Blueprint $table) {
            $table->id();
            $table->integer('zone_id');
            $table->foreignId('area_id');
            $table->foreign('area_id')->references('id')->on('areas');
            // $table->foreignId('area_id')->constrained();
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
        Schema::dropIfExists('zone_has_areas');
    }
}

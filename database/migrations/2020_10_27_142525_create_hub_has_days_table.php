<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubHasDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hub_has_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id');
            $table->foreign('hub_id')->references('id')->on('distribution_hubs');
            $table->integer('day_id');
            
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
        Schema::dropIfExists('hub_has_days');
    }
}

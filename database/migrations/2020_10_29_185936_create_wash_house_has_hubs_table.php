<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWashHouseHasHubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wash_house_has_hubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wash_house_id');
            $table->foreign('wash_house_id')->references('id')->on('wash_houses');
            $table->foreignId('hub_id');
            $table->foreign('hub_id')->references('id')->on('distribution_hubs');
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
        Schema::dropIfExists('wash_house_has_hubs');
    }
}

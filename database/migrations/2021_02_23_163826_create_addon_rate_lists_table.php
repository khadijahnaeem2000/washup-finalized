<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonRateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_rate_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('addon_id')->nullable();
            $table->integer('wash_house_id')->nullable();
            $table->integer('rate')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('addon_rate_lists');
    }
}

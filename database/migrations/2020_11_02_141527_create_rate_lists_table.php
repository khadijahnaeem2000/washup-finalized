<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateListsTable extends Migration
{
    public function up()
    {
        Schema::create('rate_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreignId('wash_house_id');
            $table->foreign('wash_house_id')->references('id')->on('wash_houses');
            $table->integer('rate')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('rate_lists');
    }
}

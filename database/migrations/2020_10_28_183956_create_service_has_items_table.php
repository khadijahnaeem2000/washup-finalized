<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceHasItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_has_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreignId('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('item_rate')->nullable();
            // $table->string('item_addon')->nullable();
            $table->boolean('item_status')->default(0);
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
        Schema::dropIfExists('service_has_items');
    }
}

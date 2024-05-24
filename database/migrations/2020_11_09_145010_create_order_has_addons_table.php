<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHasAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_has_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreignId('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreignId('addon_id');
            $table->foreign('addon_id')->references('id')->on('addons');
            
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
        Schema::dropIfExists('order_has_addons');
    }
}

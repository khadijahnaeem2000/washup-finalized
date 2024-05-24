<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHasItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_has_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreignId('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('pickup_qty')->nullable();
            $table->integer('delivery_qty')->nullable();
            $table->integer('hfq_qty')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('reason_id');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->string('item_image')->nullable();
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
        Schema::dropIfExists('order_has_items');
    }
}

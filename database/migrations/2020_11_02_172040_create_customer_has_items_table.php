<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerHasItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_has_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreignId('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->integer('item_rate')->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('customer_has_items');
    }
}

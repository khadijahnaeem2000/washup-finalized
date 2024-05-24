<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerHasRetainerDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_has_retainer_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreignId('day_id');
            $table->foreign('day_id')->references('id')->on('days');
            $table->foreignId('time_slot_id');
            $table->foreign('time_slot_id')->references('id')->on('time_slots');
            $table->string('note')->nullable();
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
        Schema::dropIfExists('customer_has_retainer_days');
    }
}

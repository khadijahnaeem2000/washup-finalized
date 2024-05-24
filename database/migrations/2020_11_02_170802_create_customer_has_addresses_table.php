<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerHasAddressesTable extends Migration
{

    public function up()
    {
        Schema::create('customer_has_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->text('address')->nullable();
            $table->float('latitude', 8, 3)->nullable();
            $table->float('longitude', 8, 3)->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('customer_has_addresses');
    }
}

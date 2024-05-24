<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidersTable extends Migration
{
    public function up()
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('cnic_no')->unique();
            $table->string('contact_no')->unique();
            $table->integer('max_loc');
            $table->string('color_code');
            $table->string('vehicle_reg_no');
            $table->foreignId('vehicle_type_id');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
            $table->text('address')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('riders');
    }
}

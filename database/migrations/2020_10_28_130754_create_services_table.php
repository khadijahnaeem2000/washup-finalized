<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('hanger')->default(0);
            $table->integer('qty');
            $table->foreignId('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->integer('rate')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('services');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('rider_id')->nullable();
            $table->date('plan_date')->nullable();
            $table->string('start_ride')->nullable();
            $table->string('end_ride')->nullable();
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
        Schema::dropIfExists('rider_histories');
    }
}

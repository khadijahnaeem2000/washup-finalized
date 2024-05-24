<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiderIncentivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rider_incentives', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('pickup_rate')->nullable();
            $table->string('pickdrop_rate')->nullable();
            $table->string('kilometer')->nullable();
            $table->string('status')->default(0)->nullable();
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
        Schema::dropIfExists('rider_incentives');
    }
}

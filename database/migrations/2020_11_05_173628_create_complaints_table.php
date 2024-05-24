<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreignId('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreignId('complaint_nature_id');
            $table->foreign('complaint_nature_id')->references('id')->on('complaint_natures');
            $table->foreignId('complaint_tag_id');
            $table->foreign('complaint_tag_id')->references('id')->on('complaint_tags');
            $table->boolean('status_id')->nullable();
            $table->string('image')->nullable();
            $table->text('complaint_detail')->nullable();
            
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
        Schema::dropIfExists('complaints');
    }
}

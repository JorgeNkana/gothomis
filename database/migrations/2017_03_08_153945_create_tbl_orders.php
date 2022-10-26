<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

		Schema::create('tbl_orders', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('priority',10)->nullable();
            $table->text('clinical_note');
            $table->integer('receiver_id')->nullable()->unsigned();
            $table->foreign('receiver_id')->references('id')->on('users');
            $table->integer('processor_id')->nullable()->unsigned();
            $table->foreign('processor_id')->references('id')->on('users');
            $table->time('time_received')->nullable();
            $table->integer('test_id',false,true)->unsigned()->nullable();
            $table->foreign('test_id')->references('id')->on('tbl_items');
			$table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('tbl_requests');
			$table->string('sample_no',50)->nullable();
			$table->string('sample_types',100)->nullable();
			$table->boolean('order_control')->nullable();
			$table->string('order_cancel_reason',200)->nullable();
			$table->boolean('order_status')->nullable();
			$table->integer('order_validator_id')->nullable()->unsigned();
            $table->foreign('order_validator_id')->references('id')->on('users');
			$table->boolean('result_control')->nullable();
			$table->boolean('eraser')->nullable();
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
        //
		Schema::dropIfExists('tbl_orders');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDispensers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dispensers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('item_id',false,true)->unsigned();           
            $table->foreign('item_id')->references('id')->on('tbl_items');
           $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('received_from_id')->unsigned();
            $table->foreign('received_from_id')->references('id')->on('tbl_store_lists');
            $table->integer('transaction_type_dispensed_id')->unsigned()->nullable();
            $table->foreign('transaction_type_dispensed_id')->references('id')->on('tbl_transaction_types');
            $table->integer('dispenser_id')->unsigned();
            $table->foreign('dispenser_id')->references('id')->on('tbl_store_lists');
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('dispensing_status_id',false,true)->unsigned()->nullable();
            $table->foreign('dispensing_status_id')->references('id')->on('tbl_store_request_statuses');
            $table->double('quantity_received')->nullable();
            $table->double('quantity_dispensed')->nullable();
            $table->double('request_amount')->nullable();
            $table->string('batch_no')->nullable();
            $table->string('control');
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
        Schema::dropIfExists('tbl_dispensers');
    }
}
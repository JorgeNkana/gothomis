<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSubStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sub_stores', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('item_id',false,true)->unsigned();           
            $table->foreign('item_id')->references('id')->on('tbl_items');
           $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
           $table->integer('received_from_id')->unsigned()->nullable();
            $table->foreign('received_from_id')->references('id')->on('tbl_store_lists');
            $table->integer('issued_store_id')->unsigned()->nullable();
            $table->foreign('issued_store_id')->references('id')->on('tbl_store_lists');
            $table->integer('requested_store_id')->unsigned()->nullable();
            $table->foreign('requested_store_id')->references('id')->on('tbl_store_lists');
            $table->double('quantity_issued')->nullable();
            $table->double('request_amount')->nullable();
            $table->integer('order_no')->unsigned()->nullable();
            $table->integer('transaction_type_id')->unsigned()->nullable();
            $table->foreign('transaction_type_id')->references('id')->on('tbl_transaction_types');
            $table->integer('request_status_id',false,true)->unsigned()->nullable();
            $table->foreign('request_status_id')->references('id')->on('tbl_store_request_statuses');
            $table->string('batch_no')->nullable();
            $table->double('quantity')->nullable();
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
        Schema::dropIfExists('tbl_sub_stores');
    }
}
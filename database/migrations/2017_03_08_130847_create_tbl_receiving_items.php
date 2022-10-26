<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblReceivingItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_receiving_items', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('item_id',false,true)->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->integer('received_store_id')->unsigned()->nullable();
            $table->foreign('received_store_id')->references('id')->on('tbl_store_lists');
            $table->integer('invoice_refference')->unsigned()->nullable();
            $table->foreign('invoice_refference')->references('id')->on('tbl_invoices');
            $table->integer('transaction_type_id')->unsigned()->nullable();
            $table->foreign('transaction_type_id')->references('id')->on('tbl_transaction_types');
            $table->integer('requesting_store_id')->unsigned()->nullable();
            $table->foreign('requesting_store_id')->references('id')->on('tbl_store_lists');
            $table->integer('internal_issuer_id')->unsigned()->nullable();
            $table->foreign('internal_issuer_id')->references('id')->on('tbl_store_lists');
            $table->integer('order_no')->unsigned()->nullable();
            $table->string('batch_no')->nullable();
            $table->string('remarks')->nullable();
            $table->double('Reorder_level')->nullable();
            $table->double('quantity')->nullable();
            $table->double('requested_amount')->nullable();
            $table->double('issued_quantity')->nullable();
            $table->date('received_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->double('price')->nullable();
           $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
           $table->integer('received_from_id')->unsigned()->nullable();
            $table->foreign('received_from_id')->references('id')->on('tbl_vendors');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
           $table->integer('attachment_id')->unsigned()->nullable();
            $table->foreign('attachment_id')->references('id')->on('tbl_attachments');
            $table->integer('request_status_id',false,true)->unsigned()->nullable();
            $table->foreign('request_status_id')->references('id')->on('tbl_store_request_statuses');
            $table->string('control');
            $table->string('control_in')->length(1)->nullable();

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
        Schema::dropIfExists('tbl_receiving_items');
    }
}
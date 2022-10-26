<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInvoiceLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_invoice_lines', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('invoice_id')->unsigned();
            $table->foreign('invoice_id')->references('id')->on('tbl_encounter_invoices');
			$table->integer('corpse_id')->unsigned()->nullable();
            $table->foreign('corpse_id')->references('id')->on('tbl_corpses');			
            $table->integer('item_type_id',false,true)->unsigned();          
            $table->foreign('item_type_id')->references('id')->on('tbl_item_type_mappeds');
            $table->integer('quantity',false,true)->length(4)->unsigned();
            $table->integer('item_price_id')->unsigned();
            $table->foreign('item_price_id')->references('id')->on('tbl_item_prices');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('status_id',false,true)->unsigned();
            $table->foreign('status_id')->references('id')->on('tbl_payment_statuses');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->double('discount',false,true)->unsigned();
            $table->integer('discount_by')->unsigned()->nullable();
            $table->foreign('discount_by')->references('id')->on('users');
            $table->integer('payment_filter',false,true)->unsigned()->nullable();
            $table->foreign('payment_filter')->references('id')->on('tbl_pay_cat_sub_categories');
            $table->string('gepg_receipt',50)->nullable();
            $table->integer('payment_method_id',false,true)->unsigned()->nullable();
            $table->foreign('payment_method_id')->references('id')->on('tbl_payment_methods');








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
        Schema::dropIfExists('tbl_invoice_lines');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDiscountReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_discount_reasons', function (Blueprint $table) {
            $table->increments('id');
            
			
			$table->string('discount_reason',200);
			
			$table->integer('receipt_number')->unsigned()->nullable();
			
			
            $table->foreign('receipt_number')->references('invoice_id')->on('tbl_invoice_lines');
			
			$table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
			
			$table->integer('facility_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
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
        Schema::dropIfExists('tbl_discount_reasons');
    }
}
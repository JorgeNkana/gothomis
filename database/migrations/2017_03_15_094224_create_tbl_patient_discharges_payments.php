<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPatientDischargesPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_patient_discharges_payments', function (Blueprint $table) {
        $table->increments('id');
        
		$table->integer('admission_id')->unsigned();
		$table->foreign('admission_id')->references('id')->on('tbl_admissions');
        $table->integer('item_transaction_id')->unsigned();
		$table->foreign('item_transaction_id')->references('id')->on('tbl_encounter_invoices');
            
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
        Schema::dropIfExists('tbl_patient_discharges_payments');
    }
}
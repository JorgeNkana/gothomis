<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPrescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_prescriptions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('item_id',false,true)->unsigned();           
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('prescriber_id')->unsigned();
            $table->foreign('prescriber_id')->references('id')->on('users');
			$table->integer('verifier_id')->unsigned()->nullable();
            $table->foreign('verifier_id')->references('id')->on('users');
            $table->integer('dispenser_id')->unsigned()->nullable();
            $table->foreign('dispenser_id')->references('id')->on('users');
			$table->integer('admission_id')->unsigned()->nullable();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
			$table->integer('visit_id')->unsigned()->nullable();
            $table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('quantity')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->string('dose')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('conservatives')->nullable();
            $table->string('start_date')->nullable();
            $table->text('instruction')->nullable();
            $table->string('out_of_stock',2)->nullable();
            $table->integer('dispensing_status')->nullable();
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
        Schema::dropIfExists('tbl_prescriptions');
    }
}
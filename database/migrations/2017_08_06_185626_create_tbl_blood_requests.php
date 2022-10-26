<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblBloodRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_blood_requests',function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('requested_by')->unsigned();
            $table->foreign('requested_by')->references('id')->on('users');
            $table->integer('processed_by')->unsigned()->nullable();
            $table->foreign('processed_by')->references('id')->on('users');
            $table->integer('visit_id')->unsigned();
            $table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('dept_id',false,true)->unsigned()->nullable();
           
            $table->foreign('dept_id')->references('id')->on('tbl_departments');
            $table->string('blood_group',12)->nullable();
            $table->string('request_reason',200)->nullable();
            $table->integer('unit_requested',false,true)->nullable();
            $table->string('priority',12)->nullable();
            $table->string('bag_no',45)->nullable();
            $table->integer('status',false,true)->default(0);
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
        Schema::dropIfExists('tbl_blood_requests');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_admissions',function (Blueprint $table){
            $table->increments('id');
            
            $table->date('admission_date');
            $table->integer('patient_id')->unsigned();
            $table->integer('account_id')->unsigned();        
			
			$table->integer('admission_status_id',false,true)->unsigned();
            
			$table->foreign('patient_id')->references('id')->on('tbl_patients');
			$table->foreign('account_id')->references('id')->on('tbl_accounts_numbers');
            $table->foreign('admission_status_id')->references('id')->on('tbl_admission_statuses');
			$table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');			
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_admissions');
    }
}
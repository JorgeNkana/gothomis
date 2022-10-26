<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //.......
		Schema::create('tbl_requests', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('doctor_id')->unsigned()->nullable();
            $table->foreign('doctor_id')->references('id')->on('users');
			$table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
		    $table->integer('admission_id')->unsigned()->nullable();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
		    $table->integer('requesting_department_id',false,true)->unsigned()->nullable();
            $table->foreign('requesting_department_id')->references('id')->on('tbl_departments');
			$table->integer('visit_date_id')->unsigned()->nullable();
            $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');
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
			Schema::dropIfExists('tbl_requests');
    }
}
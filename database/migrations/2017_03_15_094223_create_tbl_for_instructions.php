<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForInstructions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_instructions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('instructions',100)->nullable();
            $table->string('prescriptions',100)->nullable();
			$table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
			$table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('bed_id')->nullable()->unsigned();
		    $table->foreign('bed_id')->references('id')->on('tbl_beds');		   
		    $table->integer('ward_id')->nullable()->unsigned();
		    $table->foreign('ward_id')->references('id')->on('tbl_wards');
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
        Schema::dropIfExists('tbl_instructions');
    }
}
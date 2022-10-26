<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblExternalRefferals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_refferal_externals', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('patient_condition',200)->nullable();
            $table->string('preparation_needed',200)->nullable();
			$table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');        
			$table->integer('escorting_staff')->unsigned();
            $table->foreign('escorting_staff')->references('id')->on('users');        
			$table->integer('status',false,true)->length(1);
			$table->integer('sender_facility_id')->unsigned();
            $table->foreign('sender_facility_id')->references('id')->on('tbl_facilities');  
			$table->integer('reffered_by')->unsigned();
            $table->foreign('reffered_by')->references('id')->on('users');        
			
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
        Schema::dropIfExists('tbl_refferal_externals');
    }
}
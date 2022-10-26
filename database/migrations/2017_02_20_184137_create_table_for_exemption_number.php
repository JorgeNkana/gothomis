<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableForExemptionNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_exemption_numbers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('exemption_number',10);
            $table->integer('patient_id')->unsigned();
            $table->integer('facility_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
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
        Schema::dropIfExists('tbl_exemption_numbers');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAntiNatalPmtcts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_anti_natal_pmtcts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('vvu_infection',6);
            $table->string('has_counsel_given',6);
            $table->date('counselling_date')->nullable();
            $table->string('has_taken_vvu_test',6);
            $table->date('date_of_test_taken')->nullable();
            $table->string('vvu_first_test_result',8);
            $table->string('counselling_after_vvu_test',6);
            $table->string('vvu_second_test_result',8);
            $table->string('baby_feeding_counsel_given',6);
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
        Schema::dropIfExists('tbl_anti_natal_pmtcts');
    }
}
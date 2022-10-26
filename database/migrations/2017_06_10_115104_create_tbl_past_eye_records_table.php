<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPastEyeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_past_eye_records', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('past_medical_history')->nullable();
            $table->string('past_ocular_history')->nullable();
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('visit_date_id')->unsigned();
            $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');
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
        Schema::dropIfExists('tbl_past_eye_records');
    }
}
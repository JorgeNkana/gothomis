<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiagnosisDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_diagnosis_details',function (Blueprint $table){
            $table->increments('id');
            
            $table->string('status',20);
            $table->integer('diagnosis_id')->unsigned();
            $table->integer('diagnosis_description_id',false,true)->unsigned();
            $table->foreign('diagnosis_id')->references('id')->on('tbl_diagnoses');
            $table->foreign('diagnosis_description_id')->references('id')->on('tbl_diagnosis_descriptions');
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
        Schema::dropIfExists('tbl_diagnosis_details');
    }
}
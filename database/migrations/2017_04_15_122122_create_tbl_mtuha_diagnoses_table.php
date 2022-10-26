<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMtuhaDiagnosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mtuha_diagnoses', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('diagnosis_id',false,true)->unsigned();
            $table->foreign('diagnosis_id')->references('id')->on('tbl_diagnosis_descriptions');
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
        Schema::dropIfExists('tbl_mtuha_diagnoses');
    }
}
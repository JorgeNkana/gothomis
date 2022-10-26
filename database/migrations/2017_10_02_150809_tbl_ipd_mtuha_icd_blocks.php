<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblIpdMtuhaIcdBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ipd_mtuha_icd_blocks', function (Blueprint $table) {
            $table->increments('id');
			$table->string('icd_block',50)->nullable();		
			$table->Integer('ipd_mtuha_diagnosis_id')->length(10)->unsigned();		
            $table->foreign('ipd_mtuha_diagnosis_id')->references('id')->on('tbl_ipd_mtuha_diagnoses');
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
        Schema::dropIfExists('tbl_ipd_mtuha_icd_blocks');
    }
}
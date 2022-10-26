<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForMtuhaFilterDiagnosis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mtuha_diagnosis_filters', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('diagnosis_id',false,true)->unsigned()->nullable();
            $table->foreign('diagnosis_id')->references('id')->on('tbl_diagnosis_descriptions');
            $table->integer('on_off',false,true)->length(1)->nullable();
            $table->integer('opd',false,true)->length(1)->default(0);
            $table->integer('ipd',false,true)->length(1)->default(0);
            $table->integer('eye',false,true)->length(1)->default(0);
           // $table->integer('anc',false,true)->length(1)->default(0);
           // $table->integer('pnc',false,true)->length(1)->default(0);
           // $table->integer('family_planning',false,true)->length(1)->default(0);
           // $table->integer('child',false,true)->length(1)->default(0);
            $table->integer('dtc',false,true)->length(1)->default(0);
            $table->integer('dental',false,true)->length(1)->default(0);
            //$table->integer('labour',false,true)->length(1)->default(0);
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
        Schema::dropIfExists('tbl_mtuha_diagnosis_filters');
    }
}
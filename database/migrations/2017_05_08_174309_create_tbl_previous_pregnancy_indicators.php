<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPreviousPregnancyIndicators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_previous_pregnancy_indicators', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('fp_35_years_n_above',12);
            $table->string('lp_10_years_n_above',12);
            $table->string('delivery_method');
            $table->string('fbs_msb');
            $table->string('miscarriage_three_plus',12);
            $table->string('heart_disease',12);
            $table->string('diabetic',12);
            $table->string('tb',12);
            $table->string('waist_disability',12);
            $table->string('high_bleeding',12);
            $table->string('placenta_stacked',12);
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
        Schema::dropIfExists('tbl_previous_pregnancy_indicators');
    }
}
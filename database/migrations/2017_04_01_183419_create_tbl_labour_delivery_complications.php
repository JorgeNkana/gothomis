<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLabourDeliveryComplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_labour_delivery_complications', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('vaginal_bleeding',8);
            $table->string('prom',8);
            $table->string('anaemia',8);
            $table->string('preeclampsia',8);
            $table->string('eclampsia',8);
            $table->string('malaria',8);
            $table->string('sepsis',8);
            $table->string('hiv_p',8);
            $table->string('pph',8);
            $table->string('fgm',8);
            $table->string('obstructed_labour',8);
            $table->string('three_tear',8);
            $table->string('retained_placenta',8);
            $table->string('chest_pain',8);
            $table->string('loss_strength',8);
            $table->string('other_complication',100)->nullable();
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
        Schema::dropIfExists('tbl_labour_delivery_complications');
    }
}
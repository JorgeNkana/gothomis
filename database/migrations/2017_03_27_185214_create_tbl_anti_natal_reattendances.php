<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAntiNatalReattendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_anti_natal_reattendances', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('km',6);
            $table->string('anaemia',6);
            $table->string('protenuria',6);
            $table->string('bp',6);
            $table->string('kutoongezeka_uzito',6);
            $table->string('damu_ukeni',6);
            $table->string('mlalo_mbaya_wa_mtoto',6);
            $table->string('mimba_ya_nne_plus',6);
            $table->string('scisorian_section',6);
            $table->string('vaccum_extruction',6);
            $table->string('tb',6);
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
        Schema::dropIfExists('tbl_anti_natal_reattendances');
    }
}
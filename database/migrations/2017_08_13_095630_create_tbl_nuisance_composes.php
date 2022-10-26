<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblNuisanceComposes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_nuisance_composes', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('nuisance_id')->unsigned();
            $table->foreign('nuisance_id')->references('id')->on('tbl_nuisance_registers');
            $table->string('cause');
            $table->string('location');
            $table->dateTime('event_date');
            $table->string('abatement')->nullable();
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
        Schema::dropIfExists('tbl_nuisance_composes');
    }
}
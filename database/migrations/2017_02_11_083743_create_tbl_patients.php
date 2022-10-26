<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_patients', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('first_name',80)->nullable();
            $table->string('middle_name',80)->nullable();
            $table->string('last_name',80)->nullable();
            $table->date('dob');
            $table->string('gender',6);
            $table->string('medical_record_number',25)->unique();
            $table->string('mobile_number',15)->nullable();
            $table->integer('residence_id',false,true)->unsigned()->nullable();
            $table->foreign('residence_id')->references('id')->on('tbl_residences');
            $table->integer('marital_id',false,true)->unsigned()->nullable();
            $table->foreign('marital_id')->references('id')->on('tbl_maritals');
            $table->integer('occupation_id',false,true)->unsigned()->nullable();
            $table->foreign('occupation_id')->references('id')->on('tbl_occupations');
            $table->integer('tribe_id',false,true)->unsigned()->nullable();
            $table->foreign('tribe_id')->references('id')->on('tbl_tribes');
            $table->integer('country_id',false,true)->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('tbl_countries');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
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
        Schema::dropIfExists('tbl_patients');
    }
}
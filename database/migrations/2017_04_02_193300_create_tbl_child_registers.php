<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblChildRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_child_registers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('residence_id',false,true)->unsigned()->nullable;
            $table->foreign('residence_id')->references('id')->on('tbl_residences');
            $table->string('client_name');
            $table->date('dob');
            $table->string('gender',7);
            $table->float('weight');
            $table->string('midwife');
            $table->string('delivery_place');
            $table->string('mother_name');
            $table->string('father_name')->nullable();
            $table->string('serial_no',10);
            $table->string('mobile_number',20)->nullable();
            $table->string('year',4);
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
        Schema::dropIfExists('tbl_child_registers');
    }
}
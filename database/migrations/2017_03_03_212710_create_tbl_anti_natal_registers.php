<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAntiNatalRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_anti_natal_registers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('serial_no',20);
            $table->string('voucher_no',20)->nullable();
            $table->date('dob');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_patients');			
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('occupation_id',false,true)->unsigned()->nullable();
            $table->foreign('occupation_id')->references('id')->on('tbl_occupations');
            $table->integer('residence_id',false,true)->nullable();
            $table->foreign('residence_id')->references('id')->on('tbl_residences');
            $table->string('status',1)->nullable();
            $table->string('client_name');
            $table->float('height')->nullable();
            $table->integer('year',false,true)->length(4);
            $table->string('education')->nullable();


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
        Schema::dropIfExists('tbl_anti_natal_registers');
    }
}
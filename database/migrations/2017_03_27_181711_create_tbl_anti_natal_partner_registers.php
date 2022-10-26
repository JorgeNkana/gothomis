<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAntiNatalPartnerRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_anti_natal_partner_registers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('partner_name');
            $table->date('dob');
            $table->string('education');
            $table->integer('occupation_id',false,true)->unsigned()->nullable();
            $table->foreign('occupation_id')->references('id')->on('tbl_occupations');
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
        Schema::dropIfExists('tbl_anti_natal_partner_registers');
    }
}
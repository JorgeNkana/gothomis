<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTbPreEntryRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tb_pre_entry_registers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('referral_type',45);
            $table->string('client_type',45);
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
        Schema::dropIfExists('tbl_tb_pre_entry_registers');
    }
}
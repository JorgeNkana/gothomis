<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCtcPatientAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ctc_patient_addresses', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('residence_id',false,true)->unsigned()->nullable();
            $table->foreign('residence_id')->references('id')->on('tbl_residences');
            $table->string('name_ten_cell_leader',100)->nullable();
            $table->string('name_head_house_hold',20)->nullable();
            $table->integer('contact_house_hold_head',false,true)->length(1);
            $table->integer('on_off',false,true)->length(1);
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
        Schema::dropIfExists('tbl_ctc_patient_addresses');
    }
}
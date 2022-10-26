<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVctRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vct_registers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('client_from',false,true)->unsigned()->nullable();
            $table->foreign('client_from')->references('id')->on('tbl_departments');
           	$table->integer('referral_to',false,true)->unsigned()->nullable();
            $table->foreign('referral_to')->references('id')->on('tbl_departments');
            $table->string('serial_no');
            $table->string('attendance_type',12);
            $table->string('pregnancy_record',12)->nullable();
            $table->string('client_from_other',50)->nullable();
            $table->string('referral_to_other',50)->nullable();
            $table->string('counselling_type',12)->nullable();
            $table->string('agreed_vvu_test',12)->nullable();
            $table->string('counselling_after_test',12)->nullable();
            $table->string('vvu_test_result',12)->nullable();
            $table->string('participatory_test_result',12)->nullable();
            $table->string('tb_test',12)->nullable();
            $table->string('tb_test_result',12)->nullable();
            $table->string('condom_given',12)->nullable();
            $table->string('comment',50)->nullable();

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
        Schema::dropIfExists('tbl_vct_registers');
    }
}
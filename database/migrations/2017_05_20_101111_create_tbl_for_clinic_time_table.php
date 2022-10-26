<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForClinicTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_clinic_schedules', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('clinic_id',false,true)->unsigned();
            $table->foreign('clinic_id')->references('id')->on('tbl_departments');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('clinic_date')->nullable();
            $table->string('week_day',25)->nullable();
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
        Schema::dropIfExists('tbl_clinic_schedules');
    }
}
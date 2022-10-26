<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmissionStatusTracks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_admission_status_tracks',function (Blueprint $table){
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->integer('status_id',false,true)->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->foreign('status_id')->references('id')->on('tbl_admission_statuses');
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
        Schema::dropIfExists('tbl_admission_status_tracks');
    }
}
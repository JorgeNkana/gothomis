<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPostNatalWombStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_post_natal_womb_statuses', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('normal',8);
            $table->string('pain',8);
            $table->string('not_well_contriction',8);
            $table->string('lochia',12);
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
        Schema::dropIfExists('tbl_post_natal_womb_statuses');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForNurseWards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_nurse_wards', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('nurse_id')->unsigned();
            $table->foreign('nurse_id')->references('id')->on('users');
            $table->integer('ward_id')->unsigned();
            $table->foreign('ward_id')->references('id')->on('tbl_wards');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('deleted',false,true)->length(1);
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
        Schema::dropIfExists('tbl_nurse_wards');
    }
}
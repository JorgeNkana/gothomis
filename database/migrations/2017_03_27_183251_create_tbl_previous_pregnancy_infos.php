<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPreviousPregnancyInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_previous_pregnancy_infos', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('number_of_pregnancy',false,true)->length(2);
            $table->integer('number_of_delivery',false,true)->length(2);
            $table->integer('number_alive_children',false,true)->length(2);
            $table->integer('number_of_miscarriage',false,true)->length(2);
            $table->date('lnmp');
            $table->date('edd');
            $table->integer('delivery_place')->unsigned();
            $table->foreign('delivery_place')->references('id')->on('tbl_facilities');
            $table->integer('year',false,true)->length(4);
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
        Schema::dropIfExists('tbl_previous_pregnancy_infos');
    }
}
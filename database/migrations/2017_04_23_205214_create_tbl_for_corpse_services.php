<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForCorpseServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_corpse_services', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('corpse_admission_id')->unsigned();
            $table->foreign('corpse_admission_id')->references('id')->on('tbl_corpse_admissions');
           $table->integer('service_number',false,true)->unsigned();     
           
            $table->foreign('service_number')->references('id')->on('tbl_items');
           $table->integer('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('tbl_corpse_services');
    }
}
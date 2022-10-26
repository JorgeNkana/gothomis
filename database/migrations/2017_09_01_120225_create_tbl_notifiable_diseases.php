<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblNotifiableDiseases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_notifiable_diseases', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('diagnosis_id',false,true)->unsigned()->nullable();
            $table->foreign('diagnosis_id')->references('id')->on('tbl_diagnosis_descriptions');
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
        Schema::dropIfExists('tbl_notifiable_diseases');
    }
}
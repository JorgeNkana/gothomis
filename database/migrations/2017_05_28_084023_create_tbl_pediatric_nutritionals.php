<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPediatricNutritionals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_pediatric_nutritionals', function (Blueprint $table) {
         $table->increments('id');
            
        $table->integer('client_id')->unsigned();
        $table->foreign('client_id')->references('id')->on('tbl_patients');
        $table->integer('user_id')->unsigned();
        $table->foreign('user_id')->references('id')->on('users');
        $table->integer('facility_id')->unsigned();
        $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('muac',10)->nullable();
            $table->string('whz_score',40)->nullable();
            $table->string('others',12)->nullable();
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
        //
    }
}
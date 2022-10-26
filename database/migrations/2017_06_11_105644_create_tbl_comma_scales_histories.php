<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCommaScalesHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_comma_scales_histories',function (Blueprint $table){
            $table->increments('id');
            
            $table->string('eye',80)->nullable();
            $table->string('verbal',80)->nullable();
            $table->string('motor',80)->nullable();
            $table->integer('comma_scale_id')->unsigned();
            $table->foreign('comma_scale_id')->references('id')->on('tbl_comma_scales');
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
        Schema::dropIfExists('tbl_comma_scales_histories');
    }
}
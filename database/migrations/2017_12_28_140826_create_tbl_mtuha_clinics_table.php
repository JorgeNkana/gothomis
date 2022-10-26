<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMtuhaClinicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mtuha_clinics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('dept_id',false,true)->length(11)->unsigned();
            $table->foreign('dept_id')->references('id')->on('tbl_departments');
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
        Schema::dropIfExists('tbl_mtuha_clinics');
    }
}
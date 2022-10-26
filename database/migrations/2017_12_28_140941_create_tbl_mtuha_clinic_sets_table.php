<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMtuhaClinicSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mtuha_clinic_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('parent_id',false,true)->length(11)->unsigned();
            $table->foreign('parent_id')->references('id')->on('tbl_mtuha_clinics');
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
        Schema::dropIfExists('tbl_mtuha_clinic_sets');
    }
}
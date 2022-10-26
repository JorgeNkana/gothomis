<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblClinicCapacity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_clinic_capacities', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('clinic_name_id',false,true)->unsigned();
            $table->foreign('clinic_name_id')->references('id')->on('tbl_departments');
            $table->integer('capacity',false,true)->length(3);
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
        Schema::dropIfExists('tbl_clinic_capacities');
    }
}
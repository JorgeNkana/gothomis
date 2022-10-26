<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVaccinationRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vaccination_registers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('vaccination_name');
            $table->string('vaccination_type');
            $table->integer('dose',false,true)->length(1)->nullable();
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
        Schema::dropIfExists('tbl_vaccination_registers');
    }
}
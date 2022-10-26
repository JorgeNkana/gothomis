<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVitals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vitals', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('vital_name',50)->nullable();
            $table->string('maximum',50)->nullable();
            $table->string('minimum',50)->nullable();
            $table->string('si_unit',12)->nullable();
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
        Schema::dropIfExists('tbl_vitals');
    }
}
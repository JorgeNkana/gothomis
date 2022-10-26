<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNextOfKins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_next_of_kins', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->string('next_of_kin_name',50);
            $table->string('mobile_number',15)->nullable();
            $table->integer('residence_id',false,true)->unsigned()->nullable();
            $table->foreign('residence_id')->references('id')->on('tbl_residences');
            $table->string('relationship',20);

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
        Schema::dropIfExists('tbl_next_of_kins');
    }
}
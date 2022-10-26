<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblOutputObservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
   Schema::create('tbl_output_observations', function (Blueprint $table) {
   $table->increments('id');
   
   $table->integer('admission_id')->unsigned();
   $table->foreign('admission_id')->references('id')->on('tbl_admissions');			
   $table->integer('observation_output_type_id')->unsigned();
   $table->foreign('observation_output_type_id')->references('id')->on('tbl_observations_output_types');
   $table->float('amount',false,true)->unsigned();
   $table->string('si_units',30);
   $table->string('treatment_remarks',100)->nullable();
   $table->integer('nurse_id')->unsigned();
   $table->foreign('nurse_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_output_observations');
    }
}
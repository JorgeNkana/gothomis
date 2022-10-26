<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForObservationsCharts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_observation_charts', function (Blueprint $table) {
          $table->increments('id');
          
		  $table->integer('admission_id')->unsigned();
          $table->foreign('admission_id')->references('id')->on('tbl_admissions');		
          $table->integer('observation_type_id')->unsigned();
          $table->foreign('observation_type_id')->references('id')->on('tbl_observation_types');
          $table->float('observed_amount',false,true)->length(4);
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
        Schema::dropIfExists('tbl_observation_charts');
    }
}
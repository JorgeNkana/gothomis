<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForPatientTeeths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_teeth_patients', function (Blueprint $table) {
          $table->increments('id');
          
		  $table->integer('dental_id',false,true)->unsigned();
          $table->foreign('dental_id')->references('id')->on('tbl_teeth_arrangements');	
		  $table->integer('dental_status',false,true)->length(1)->unsigned();
		  $table->string('css_class',45);
		  $table->integer('erasor',false,true)->length(1)->unsigned();
		  $table->integer('admission_id')->unsigned();
          $table->foreign('admission_id')->references('id')->on('tbl_admissions');	
		  $table->string('other_information',150)->nullable();
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
        Schema::dropIfExists('tbl_teeth_patients');
    }
}
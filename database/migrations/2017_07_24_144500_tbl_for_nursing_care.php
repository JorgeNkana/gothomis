<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblForNursingCare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_nursing_cares', function (Blueprint $table) {
             $table->increments('id');
             
             $table->date('date_planned')->nullable();
             $table->string('time_planned',8)->nullable();
             $table->string('diagnosis_name',150)->nullable();
             $table->string('objective',100)->nullable();
             $table->string('implementation',100)->nullable();
             $table->string('evaluation',100)->nullable();
             $table->integer('admission_id')->unsigned();
             $table->foreign('admission_id')->references('id')->on('tbl_admissions');    
             $table->integer('user_id')->unsigned();
             $table->foreign('user_id')->references('id')->on('users');
             $table->integer('facility_id')->unsigned();
             $table->foreign('facility_id')->references('id')->on('tbl_facilities');                     
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
        Schema::dropIfExists('tbl_nursing_cares');
    }
}
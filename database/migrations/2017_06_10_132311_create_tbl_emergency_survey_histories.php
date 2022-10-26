<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEmergencySurveyHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_emergency_survey_histories',function (Blueprint $table){
            $table->increments('id');
            
            $table->string('appearance',80)->nullable();
            $table->string('airway',80)->nullable();
            $table->string('breathing',80)->nullable();
            $table->string('circulation',80)->nullable();
            $table->string('disability',80)->nullable();
            $table->string('exposure',80)->nullable();
            $table->string('intervention',80)->nullable();
            $table->integer('survey_history_id')->unsigned();
            $table->foreign('survey_history_id')->references('id')->on('tbl_survey_histories');
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
            Schema::dropIfExists('tbl_emergency_survey_histories');
        }

}
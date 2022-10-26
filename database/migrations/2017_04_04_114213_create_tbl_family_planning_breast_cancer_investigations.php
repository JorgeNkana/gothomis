<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblFamilyPlanningBreastCancerInvestigations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_fplanning_breast_cancer_investigations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('bunje',8);
            $table->string('wound',8);
            $table->string('breast_bleeding',8);
            $table->string('breast_abscess',8);
            $table->string('others',100)->nullable();
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
        Schema::dropIfExists('tbl_family_planning_breast_cancer_investigations');
    }
}
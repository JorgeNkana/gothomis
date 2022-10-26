<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEmergenceVisit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_emergence_visits', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('emergency_arrival',20)->nullable();
            $table->string('referred_by',20)->nullable();
            $table->string('chief_complaint',20)->nullable();
            $table->string('triage_impression',20)->nullable();
            $table->string('disposition',20)->nullable();
            $table->string('condition_dispo',20)->nullable();
            $table->string('mode_departure',20)->nullable();
            $table->string('arrival',20)->nullable();
            $table->string('visit_type',20)->nullable();
            $table->string('acuity',20)->nullable();
            $table->string('rm',20)->nullable();
            $table->string('emmergency_dispo',20)->nullable();
            $table->string('dispo_decision',20)->nullable();
            $table->string('time_left')->nullable();
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('registered_by')->nullable()->unsigned();
            $table->foreign('registered_by')->references('id')->on('users');
            $table->integer('facility_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->date('date_attended',35)->nullable();
            $table->time('time_attended')->nullable();
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
        Schema::dropIfExists('tbl_emergence_visits');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCorpses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_corpses', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('first_name',80)->nullable();
            $table->string('middle_name',80)->nullable();
            $table->string('last_name',80)->nullable();
            $table->string('immediate_cause',150)->nullable();
            $table->string('underlying_cause',150)->nullable();
            $table->string('kin',150)->nullable();
            $table->string('transport',50)->nullable();
            $table->string('death_certifier',50)->nullable();
            $table->string('time_of_death_certifier',50)->nullable();
            $table->string('police_mobile_no',50)->nullable();
            $table->string('police_station',50)->nullable();
            $table->string('driver',150)->nullable();
            $table->date('dob')->nullable();
			$table->date('dod')->nullable();
            $table->string('gender',6)->nullable();
            $table->string('corpse_record_number',25);
            $table->string('mobile_number',15)->nullable();
            $table->string('corpse_taken_by',150)->nullable();
            $table->string('corpse_conditions',150)->nullable();
            $table->string('corpse_properties',150)->nullable();
            $table->integer('residence_id',false,true)->unsigned();
            $table->foreign('residence_id')->references('id')->on('tbl_residences');
            $table->integer('marital_id',false,true)->unsigned()->nullable();
            $table->foreign('marital_id')->references('id')->on('tbl_maritals');
            $table->integer('occupation_id',false,true)->unsigned()->nullable();
            $table->foreign('occupation_id')->references('id')->on('tbl_occupations');
            $table->integer('tribe_id',false,true)->unsigned()->nullable();
            $table->foreign('tribe_id')->references('id')->on('tbl_tribes');
            $table->integer('country_id',false,true)->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('tbl_countries');
            $table->integer('facility_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');           
			
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
        Schema::dropIfExists('tbl_corpses');
    }
}
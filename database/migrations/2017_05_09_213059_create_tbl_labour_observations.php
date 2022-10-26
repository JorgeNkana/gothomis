<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLabourObservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_labour_observations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->dateTime('labour_start_date')->nullable();
            $table->string('amniotic_bust')->nullable();
            $table->dateTime('amniotic_bust_date')->nullable();
            $table->string('baby_possition')->nullable();
            $table->string('baby_pointer')->nullable();
            $table->string('sacral_promontary_reached')->nullable();
            $table->string('ischial_spine_apeared')->nullable();
            $table->string('narrow_outlet')->nullable();
            $table->string('large_servix')->nullable();
            $table->float('temperature')->nullable();
            $table->float('bp')->nullable();
            $table->float('hb')->nullable();
            $table->string('blood_bleeding')->nullable();
            $table->string('baby_heart_beat')->nullable();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('tbl_labour_observations');
    }
}
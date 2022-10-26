<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAntiNatalAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_anti_natal_attendances', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->float('weight');
            $table->float('hb');
            $table->float('bp');
            $table->string('urine_albumin',12);
            $table->string('urine_sugar',12);
            $table->float('pregnancy_height')->nullable();
            $table->string('baby_position',20)->nullable();
            $table->string('baby_pointer',20)->nullable();
            $table->string('baby_play',20)->nullable();
            $table->string('baby_heart_beat',20)->nullable();
            $table->string('oedema',20)->nullable();
            $table->string('twins',20)->nullable();
            $table->date('followup_date');
            
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
        Schema::dropIfExists('tbl_anti_natal_attendances');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblClientsComplains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_clients_complains', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
             $table->integer('complain_area_id',false,true)->unsigned()->nullable();
           
            $table->foreign('complain_area_id')->references('id')->on('tbl_departments');
            $table->string('complain');
            $table->string('immediate_measure')->nullable();
            $table->string('solution')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('tbl_clients_complains');
    }
}
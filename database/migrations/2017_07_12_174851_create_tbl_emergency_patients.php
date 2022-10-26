<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEmergencyPatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_emergency_patients', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('visiting_id')->nullable()->unsigned();
            $table->foreign('visiting_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('emergency_type_id')->unsigned()->nullable();
            $table->foreign('emergency_type_id')->references('id')->on('tbl_emergency_types');
            $table->integer('registered_by')->nullable()->unsigned();
            $table->foreign('registered_by')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_emergency_patients');

    }
}
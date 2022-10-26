<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaHpi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_hpi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id',false,true)->length(10)->nullable(false);
            $table->timestamp('date_of_injury')->nullable();
            $table->string('place_of_injury',360);
            $table->string('pre_hospital_care',360);
            $table->string('activity_during_injury',360);
            $table->foreign('client_id')->references('id')->on('tbl_trauma_clients');
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
        Schema::dropIfExists('tbl_trauma_hpi');
    }
}
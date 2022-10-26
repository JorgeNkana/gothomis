<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaReassessmentVitals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_reassessment_vitals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id',false,true)->length(10)->nullable(false)->default(0);
            $table->timestamp('time');
            $table->string('temp',60);
            $table->string('bp',60);
            $table->string('hr',60);
            $table->string('rr',60);
            $table->string('spo2',60);
            $table->string('condition',360);
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
        Schema::dropIfExists('tbl_trauma_reassessment_vitals');
    }
}
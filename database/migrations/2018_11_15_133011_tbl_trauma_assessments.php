<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaAssessments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id',false,true)->length(10)->nullable(false);
            $table->string('assessment',960);
            $table->string('consultants',960);
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
        Schema::dropIfExists('tbl_trauma_assessments');
    }
}
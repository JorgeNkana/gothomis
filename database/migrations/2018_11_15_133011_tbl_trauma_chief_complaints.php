<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaChiefComplaints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_chief_complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id',false,true)->length(10)->nullable(false)->default(0);
            $table->integer('concept_id',false,true)->length(10)->nullable(false)->default(0);
            $table->string('concept_value',350);
            $table->foreign('concept_id')->references('id')->on('tbl_trauma_concepts');
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
        Schema::dropIfExists('tbl_trauma_chief_complaints');
    }
}
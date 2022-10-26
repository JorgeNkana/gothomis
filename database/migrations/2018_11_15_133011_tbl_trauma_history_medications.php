<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaHistoryMedications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_history_medications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id',false,true)->length(10)->nullable(false);
            $table->integer('concept_id',false,true)->length(10)->nullable();
            $table->string('concept_value',160);
            $table->foreign('client_id')->references('id')->on('tbl_trauma_clients');
            $table->foreign('concept_id')->references('id')->on('tbl_trauma_concepts');
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
        Schema::dropIfExists('tbl_trauma_history_medications');
    }
}
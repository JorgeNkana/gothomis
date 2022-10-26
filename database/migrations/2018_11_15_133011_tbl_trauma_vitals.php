<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaVitals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_vitals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id',false,true)->length(10)->nullable(false)->default(0);
            $table->string('temp',60)->nullable();
            $table->string('bp',60)->nullable();
            $table->string('hr',60)->nullable();
            $table->string('rr',60)->nullable();
            $table->string('spo2',60)->nullable();
            $table->string('weight',60)->nullable();
            $table->string('height',60)->nullable();
            $table->string('pd',60)->nullable();
            $table->integer('recorded_by')->nullable()->unsigned();
            $table->foreign('recorded_by')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_trauma_vitals');
    }
}
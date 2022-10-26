<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblIcuEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_icu_entries', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('admission_id')->unsigned()->nullable();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
            $table->integer('doctor_id')->unsigned()->nullable();
            $table->foreign('doctor_id')->references('id')->on('users');
            $table->integer('icu_status_id')->unsigned()->nullable();
            $table->foreign('icu_status_id')->references('id')->on('tbl_icu_statuses');
            $table->date('date_admitted',35);
            $table->string('from',5)->nullable();
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
        Schema::dropIfExists('tbl_icu_entries');
    }
}
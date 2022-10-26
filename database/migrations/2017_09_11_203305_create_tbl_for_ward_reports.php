<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForWardReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ward_reports', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('admission_status_id_1',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_1')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_2',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_2')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_3',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_3')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_4',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_4')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_5',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_5')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_6',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_6')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_7',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_7')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_8',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_8')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_9',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_9')->references('id')->on('tbl_admission_statuses');
            $table->integer('admission_status_id_10',false,true)->unsigned()->nullable();
            $table->foreign('admission_status_id_10')->references('id')->on('tbl_admission_statuses');
            $table->integer('ward_id')->unsigned();
            $table->foreign('ward_id')->references('id')->on('tbl_wards');
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
        Schema::dropIfExists('tbl_ward_reports');
    }
}
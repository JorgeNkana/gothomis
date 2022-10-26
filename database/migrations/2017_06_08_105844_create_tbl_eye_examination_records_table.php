<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEyeExaminationRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_eye_examination_records', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('description')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('category')->nullable();
            $table->string('hand_movement')->nullable();
            $table->string('perception_light')->nullable();
            $table->string('non_perception_light')->nullable();
            $table->integer('clinic_visit_id')->unsigned();
            $table->foreign('clinic_visit_id')->references('id')->on('tbl_eyeclinic_visits');
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
        Schema::dropIfExists('tbl_eye_examination_records');
    }
}
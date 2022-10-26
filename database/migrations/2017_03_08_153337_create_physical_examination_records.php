<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysicalExaminationRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_physical_examination_records',function (Blueprint $table){
            $table->increments('id');
            
            $table->text('observation')->nullable();
            $table->text('category')->nullable();
            $table->text('system')->nullable();
            $table->text('local_examination')->nullable();
            $table->text('gen_examination')->nullable();
            $table->text('summary_examination')->nullable();
            $table->text('other_systems_summary')->nullable();
            $table->integer('physical_examination_id')->unsigned();
            $table->foreign('physical_examination_id')->references('id')->on('tbl_physical_examinations');
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
        Schema::dropIfExists('tbl_physical_examination_records');
    }
}
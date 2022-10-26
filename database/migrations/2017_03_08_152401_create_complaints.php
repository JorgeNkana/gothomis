<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplaints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_complaints', function (Blueprint $table){
            $table->increments('id');
            
            $table->string('description',200)->nullable();
            $table->string('duration',5)->nullable();
            $table->string('duration_unit',7)->nullable();
            $table->string('status',20)->nullable();
            $table->text('other_complaints')->nullable();
            $table->text('hpi')->nullable();
            $table->integer('history_exam_id')->unsigned();
            $table->foreign('history_exam_id')->references('id')->on('tbl_history_examinations');
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
        Schema::dropIfExists('tbl_complaints');
    }
}
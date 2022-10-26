<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePastMedicalRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_past_medical_records',function (Blueprint $table){
            $table->increments('id');
            
            $table->text('descriptions')->nullable();
            $table->string('status')->nullable();
            $table->text('surgeries')->nullable();
            $table->text('admissions')->nullable();
            $table->text('transfusion')->nullable();
            $table->text('immunisation')->nullable();
            $table->integer('past_medical_history_id')->unsigned();
            $table->foreign('past_medical_history_id')->references('id')->on('tbl_past_medical_histories');
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
        Schema::dropIfExists('tbl_past_medical_records');
    }
}
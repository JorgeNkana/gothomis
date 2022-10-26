<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblIntraOperationConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_intra_opconditions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('erasor',false,true)->length(1)->unsigned();
            $table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
            $table->integer('request_id')->unsigned();
            $table->foreign('request_id')->references('id')->on('tbl_theatre_waits');
            $table->string('noted_value',100)->nullable();
            $table->string('time_taken',10)->nullable();
            $table->string('am_pm',2)->nullable();
            $table->string('information_category',150)->nullable();
            $table->integer('nurse_id')->unsigned();
            $table->foreign('nurse_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_intra_opconditions');
    }
}
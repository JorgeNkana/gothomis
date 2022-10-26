<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblReattendanceFreeDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_reattendance_free_days', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('facility_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('days')->unsigned();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('tbl_reattendance_free_days');
    }
}
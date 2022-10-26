<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTurningChart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_turning_charts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('visit_date_id')->unsigned();
            $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
            $table->string('date_recorded',10);
            $table->string('time_recorded',7);
            $table->string('position',100);
            $table->string('remarks',100);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_turning_charts');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPastEntHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_past_ent_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id',false,true)->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('visit_date_id',false,true)->unsigned();
            $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('user_id',false,true)->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id',false,true)->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('admission_id',false,true)->unsigned()->nullable();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
            $table->string('past_ent');
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
        Schema::dropIfExists('tbl_past_ent_histories');
    }
}
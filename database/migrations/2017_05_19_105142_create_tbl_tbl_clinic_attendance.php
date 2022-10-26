<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTblClinicAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_clinic_attendances', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('refferal_id')->unsigned()->nullable();
            $table->foreign('refferal_id')->references('id')->on('tbl_clinic_instructions');
            $table->integer('visit_id')->unsigned();
            $table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
            $table->date('next_visit')->nullable();
            $table->integer('follow_up_status')->unsigned()->nullable();
            $table->foreign('follow_up_status')->references('id')->on('tbl_follow_up_statuses');
            $table->integer('clinic_capacity')->unsigned()->nullable();
            $table->foreign('clinic_capacity')->references('id')->on('tbl_clinic_capacities');
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
        Schema::dropIfExists('tbl_clinic_attendances');
    }
}
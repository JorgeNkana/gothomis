<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_referrals', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('visit_id')->unsigned();           
			$table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('referral_type',false,true)->length(1)->unsigned();
            $table->integer('status',false,true)->length(1)->unsigned();
            $table->string('summary',100);
            $table->integer('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on('users');
            $table->integer('from_facility_id')->unsigned();
            $table->foreign('from_facility_id')->references('id')->on('tbl_facilities');
            $table->integer('to_facility_id')->unsigned();
            $table->foreign('to_facility_id')->references('id')->on('tbl_facilities');
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
        Schema::dropIfExists('tbl_referrals');
    }
}
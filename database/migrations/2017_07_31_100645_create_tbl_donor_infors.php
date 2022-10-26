<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDonorInfors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_donor_infors', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->string('post_address',30)->nullable();
            $table->string('phy_address',30)->nullable();
            $table->string('fax',30)->nullable();
            $table->string('donor_no',30)->nullable();
            $table->string('donor_type',30)->nullable();
            $table->string('donor_condition',30)->nullable();
            $table->string('last_donation_date',30)->nullable();
            $table->string('last_donation_place')->nullable();
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
        Schema::dropIfExists('tbl_donor_infors');
    }
}
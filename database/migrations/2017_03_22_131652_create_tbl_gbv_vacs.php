<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblGbvVacs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_gbv_vacs', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
             $table->integer('referral_id')->unsigned()->nullable();
            $table->foreign('referral_id')->references('id')->on('tbl_referral_institutions');
            $table->integer('violence_category_id',false,true)->unsigned();
            $table->foreign('violence_category_id')->references('id')->on('tbl_violence_categories');
            $table->integer('violence_type_id',false,true)->unsigned();
            $table->foreign('violence_type_id')->references('id')->on('tbl_violence_types');
           $table->integer('attachment_id')->unsigned()->nullable();
            $table->foreign('attachment_id')->references('id')->on('tbl_attachments');
            $table->date('followup_date')->nullable();
            $table->date('date_of_event');
            $table->string('description')->nullable();
            $table->string('service')->nullable();
            $table->string('other_description')->nullable();
            $table->string('referral_reason')->nullable();

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
        Schema::dropIfExists('tbl_gbv_vacs');
    }
}
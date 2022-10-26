<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblClientViolences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_client_violences', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('violence_type_id',false,true)->unsigned();
            $table->foreign('violence_type_id')->references('id')->on('tbl_violence_types');
            $table->integer('sub_violence_id',false,true)->unsigned();
            $table->foreign('sub_violence_id')->references('id')->on('tbl_violence_sub_categories');
            $table->integer('violence_category_id',false,true)->unsigned();
            $table->foreign('violence_category_id')->references('id')->on('tbl_violence_categories');
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->dateTime('event_date')->nallable();
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
        Schema::dropIfExists('tbl_client_violences');
    }
}
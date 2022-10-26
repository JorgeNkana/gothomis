<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObsGynRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_obs_gyn_records',function (Blueprint $table){
            $table->increments('id');            
            $table->text('menarche')->nullable();
            $table->text('menopause')->nullable();
            $table->text('menstrual_cycles')->nullable();
            $table->text('std')->nullable();
            $table->text('abortions')->nullable();
            $table->text('contraceptives')->nullable();
            $table->text('due_date')->nullable();
            $table->text('lnmp')->nullable();
            $table->text('cycle')->nullable();
            $table->text('period')->nullable();
            $table->text('gravidity')->nullable();
            $table->text('parity')->nullable();
            $table->text('living_children')->nullable();
            $table->text('gestational_age')->nullable();
            $table->text('category')->nullable();
            $table->integer('obs_gyn_id')->unsigned();
            $table->foreign('obs_gyn_id')->references('id')->on('tbl_obs_gyns');
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
        Schema::dropIfExists('tbl_obs_gyn_records');
    }
}
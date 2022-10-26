<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewOfSystems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_review_of_systems',function(Blueprint $table){
            $table->increments('id');
            
            $table->string('status',50)->nullable();
            $table->integer('review_system_id')->unsigned()->nullable();
            $table->foreign('review_system_id')->references('id')->on('tbl_review_systems');
            $table->integer('system_id')->unsigned()->nullable();
            $table->foreign('system_id')->references('id')->on('tbl_body_systems');
            $table->text('review_summary')->nullable();
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
        Schema::dropIfExists('tbl_review_of_systems');
    }
}
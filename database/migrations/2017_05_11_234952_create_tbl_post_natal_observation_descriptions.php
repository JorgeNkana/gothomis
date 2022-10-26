<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPostNatalObservationDescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_post_natal_observation_descriptions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('observation_id')->unsigned();
            $table->foreign('observation_id')->references('id')->on('tbl_post_natal_observation_lists');
           $table->string('observation');
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
        Schema::dropIfExists('tbl_post_natal_observation_descriptions');
    }
}
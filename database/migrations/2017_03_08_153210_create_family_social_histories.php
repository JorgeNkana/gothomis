<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFamilySocialHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_family_social_histories',function (Blueprint $table){
            $table->increments('id');
            
            $table->text('chronic_illness')->nullable();
            $table->text('substance_abuse')->nullable();
            $table->text('adoption')->nullable();
            $table->text('others')->nullable();
            $table->integer('family_history_id')->unsigned();
            $table->foreign('family_history_id')->references('id')->on('tbl_family_histories');
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
        Schema::dropIfExists('tbl_family_social_historys');
    }
}
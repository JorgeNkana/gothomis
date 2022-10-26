<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBirthChildHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_child_birth_histories',function (Blueprint $table){
            $table->increments('id');
            
            $table->text('antenatal');
            $table->text('natal');
            $table->text('post_natal');
            $table->text('nutrition');
            $table->text('growth');
            $table->text('development');
            $table->integer('birth_history_id')->unsigned();
            $table->foreign('birth_history_id')->references('id')->on('tbl_birth_histories');
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
        Schema::dropIfExists('tbl_child_birth_historys');
    }
}
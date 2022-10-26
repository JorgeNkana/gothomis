<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForPerformances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_system_perfomances', function (Blueprint $table) {
			$table->increments('id');
            
			$table->integer('user_id' )->unsigned();
			$table->integer('login_id')->unsigned();
            $table->foreign('login_id')->references('id')->on('tbl_last_logins');
            $table->integer('number_of_activities',false,true)->nullable();
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
        Schema::dropIfExists('tbl_system_perfomances');
    }
}
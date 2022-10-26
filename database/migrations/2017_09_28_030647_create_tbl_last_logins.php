<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLastLogins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_last_logins', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('user_id' )->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ip',45)->nullable();
            $table->string('mac_address',45)->nullable();
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
        Schema::dropIfExists('tbl_last_logins');
    }
}
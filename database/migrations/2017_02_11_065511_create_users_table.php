<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('name');
            $table->integer('loggedIn',false,true)->length(1);
            $table->string('ip',45)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile_number');
            $table->string('gender');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('proffesionals_id',false,true)->unsigned();
            $table->foreign('proffesionals_id')->references('id')->on('tbl_proffesionals');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
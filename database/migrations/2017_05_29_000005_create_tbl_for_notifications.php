<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_notifications', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('receiver_id')->nullable()->unsigned();
            $table->foreign('receiver_id')->references('id')->on('users');
            $table->integer('sender_id')->unsigned()->nullable();
            $table->foreign('sender_id')->references('id')->on('users');
            $table->string('message',200)->nullable();
            $table->integer('delete_sms',false,true)->length(1);
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
        Schema::dropIfExists('tbl_notifications');
    }
}
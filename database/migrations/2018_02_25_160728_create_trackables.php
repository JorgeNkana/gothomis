<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('trackables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id',false,true);
            $table->integer('patient_id',false,true);
            $table->text('action');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->integer('trackable_id',false,true);
            $table->text('trackable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('trackables');
    }
}
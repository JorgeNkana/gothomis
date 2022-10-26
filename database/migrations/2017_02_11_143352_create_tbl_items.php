<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_name');
            $table->integer('dept_id',false,true)->unsigned();
            $table->foreign('dept_id')->references('id')->on('tbl_departments');

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
        Schema::dropIfExists('tbl_items');
    }
}
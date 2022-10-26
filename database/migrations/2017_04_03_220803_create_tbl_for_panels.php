<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForPanels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_panels', function (Blueprint $table) {
            $table->increments('id');
            
             $table->integer('item_id',false,true)->unsigned();           
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->integer('equipment_id')->unsigned();
            $table->foreign('equipment_id')->references('id')->on('tbl_equipments');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('panel_name',80);
            $table->boolean('erasor');
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
        Schema::dropIfExists('tbl_panels');
    }
}
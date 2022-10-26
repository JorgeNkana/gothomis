<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInventoryIssuingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inventory_issuings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('quantity',false,true);
            $table->integer('item_received_id')->unsigned();
            $table->foreign('item_received_id')->references('id')->on('tbl_inventory_receivings');
            $table->integer('issuing_officer_id')->unsigned();
            $table->foreign('issuing_officer_id')->references('id')->on('users');
            $table->integer('receiver_id')->unsigned();
            $table->foreign('receiver_id')->references('id')->on('users');
            $table->integer('department_id',false,true)->unsigned()->nullable();
           
            $table->foreign('department_id')->references('id')->on('tbl_departments');
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
        Schema::dropIfExists('tbl_inventory_issuings');
    }
}
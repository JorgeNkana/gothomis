<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInventoryRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inventory_requests', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('quantity',false,true);
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_inventory_items');
             $table->integer('department_id',false,true)->unsigned()->nullable();
           
            $table->foreign('department_id')->references('id')->on('tbl_departments');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('status',false,true);
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
        Schema::dropIfExists('tbl_inventory_requests');
    }
}
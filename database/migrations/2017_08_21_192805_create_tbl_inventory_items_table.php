<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInventoryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inventory_items', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->integer('item_type_id')->unsigned();
            $table->foreign('item_type_id')->references('id')->on('tbl_ledgers');
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
        Schema::dropIfExists('tbl_inventory_items');
    }
}
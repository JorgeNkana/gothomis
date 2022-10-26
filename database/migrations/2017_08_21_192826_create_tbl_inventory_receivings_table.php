<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInventoryReceivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inventory_receivings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('batch',15)->nullable();
            $table->integer('quantity',false,true);
            $table->integer('cost_price',false,true)->nullable();
            $table->string('description')->nullable();
            $table->string('asset_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_inventory_items');
            $table->string('supplier',200)->nullable();
            $table->integer('control_balance',false,true)->nullable();
            $table->integer('order_status',false,true)->default(0);
            $table->integer('order_number')->unsigned();
            $table->foreign('order_number')->references('id')->on('tbl_inventory_orders');
            $table->string('receive_type',200)->default('normal');
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
        Schema::dropIfExists('tbl_inventory_receivings');
    }
}
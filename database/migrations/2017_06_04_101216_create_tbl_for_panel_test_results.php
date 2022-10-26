<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForPanelTestResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_panel_components_results', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('tbl_orders');
            $table->integer('item_id',false,true)->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->integer('component_id')->unsigned()->nullable();
            $table->foreign('component_id')->references('id')->on('tbl_testspanels');
            $table->string('sample_no',50)->nullable();
            $table->string('component_name_value',50);
            $table->string('component_name',50)->nullable();
            $table->string('minimum_limit',50)->nullable();
            $table->string('maximum_limit',50)->nullable();
            $table->string('si_units',50)->nullable();
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
        Schema::dropIfExists('tbl_panel_components_results');
    }
}
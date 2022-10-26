<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		 Schema::create('tbl_tests', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('item_id',false,true)->unsigned()->nullable();
            $table->foreign('item_id')->references('id')->on('tbl_items');
			$table->string('item_test_range',50)->nullable();
			$table->integer('item_panel_id')->unsigned()->nullable();
            $table->foreign('item_panel_id')->references('id')->on('tbl_lab_test_panels');
			$table->integer('item_unit_id')->unsigned()->nullable();
            $table->foreign('item_unit_id')->references('id')->on('tbl_units');
			$table->integer('item_test_indicator')->unsigned()->nullable();
            $table->foreign('item_test_indicator')->references('id')->on('tbl_lab_test_indicators');
			$table->integer('sample_to_collect')->unsigned()->nullable();
            $table->foreign('sample_to_collect')->references('id')->on('tbl_lab_sample_to_collects');
            $table->integer('sub_department_id')->unsigned()->nullable();
            $table->foreign('sub_department_id')->references('id')->on('tbl_sub_departments');
            $table->integer('equipment_id')->unsigned()->nullable();
            $table->foreign('equipment_id')->references('id')->on('tbl_equipments');
			$table->boolean('eraser')->nullable();
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
        //
		Schema::dropIfExists('tbl_tests');
    }
}
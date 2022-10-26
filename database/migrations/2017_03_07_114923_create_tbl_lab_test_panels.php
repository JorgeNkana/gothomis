<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLabTestPanels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('tbl_lab_test_panels', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('panel_name',30);
            $table->string('Item_test_range',30);
			$table->integer('Item_unit')->unsigned()->nullable();
			$table->foreign('Item_unit')->references('id')->on('tbl_units');
			$table->integer('Test_indicator')->unsigned()->nullable();
			$table->foreign('Test_indicator')->references('id')->on('tbl_lab_test_indicators');
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
		Schema::dropIfExists('tbl_lab_test_panels');
    }
}
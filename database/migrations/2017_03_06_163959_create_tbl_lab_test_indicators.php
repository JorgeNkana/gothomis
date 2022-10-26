<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLabTestIndicators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
Schema::create('tbl_lab_test_indicators', function (Blueprint $table) {
$table->increments('id');

$table->string('indicator',30);
$table->integer('color_id')->unsigned()->nullable();
$table->foreign('color_id')->references('id')->on('tbl_colors');
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
		Schema::dropIfExists('tbl_lab_test_indicators');
    }
}
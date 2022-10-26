<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCabinets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('tbl_cabinets', function (Blueprint $table) {
	$table->increments('id');
    
	$table->string('cabinet_name',50);
	$table->integer('mortuary_id')->unsigned()->nullable();
	$table->foreign('mortuary_id')->references('id')->on('tbl_mortuaries');
	$table->integer('user_id')->unsigned()->nullable();
	$table->foreign('user_id')->references('id')->on('users');
	$table->integer('capacity',false,true)->length(3)->unsigned()->nullable();			
	$table->integer('occupied',false,true)->length(1)->unsigned()->nullable();			
	$table->integer('eraser',false,true)->length(1)->unsigned();
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
        Schema::dropIfExists('tbl_cabinets');
    }
}
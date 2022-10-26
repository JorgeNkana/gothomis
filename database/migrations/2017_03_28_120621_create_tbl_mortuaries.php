<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblMortuaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('tbl_mortuaries', function (Blueprint $table) {
	$table->increments('id');
    
	$table->string('mortuary_name',50)->nullable();   
	$table->integer('mortuary_class_id',false,true)->unsigned()->nullable();
	$table->foreign('mortuary_class_id')->references('id')->on('tbl_items');
	$table->integer('facility_id')->unsigned()->nullable();
	$table->foreign('facility_id')->references('id')->on('tbl_facilities');
	$table->integer('user_id')->unsigned()->nullable();
	$table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_mortuaries');
    }
}
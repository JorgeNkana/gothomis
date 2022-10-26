<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForWardNursesRoaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_nurses_wards', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('nurse_id')->unsigned();
		    $table->foreign('nurse_id')->references('id')->on('users');
		    $table->integer('ward_id')->unsigned();
		    $table->foreign('ward_id')->references('id')->on('tbl_wards');
		    $table->date('starting_date');
		    $table->date('ending_date');
			$table->string('starting_time');
			$table->string('ending_time');		    
			$table->integer('finished',false,true)->length(1);
            $table->integer('incharge_id')->unsigned();
		    $table->foreign('incharge_id')->references('id')->on('users');		    
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
        Schema::dropIfExists('tbl_nurses_wards');
    }
}
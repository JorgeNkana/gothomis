<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForWardsNurses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wards_nurses', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('ward_id')->nullable()->unsigned();
		    $table->foreign('ward_id')->references('id')->on('tbl_wards');
            $table->integer('nurse_id')->unsigned();
		    $table->foreign('nurse_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_wards_nurses');
    }
}
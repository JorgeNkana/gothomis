<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForTeeths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_teeth_arrangements', function (Blueprint $table) {
            $table->increments('id');
         	$table->string('teeth_number',2)->nullable();
			$table->string('teeth_position',1)->nullable();
			$table->integer('erasor',false,true)->length(1)->unsigned();
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
        Schema::dropIfExists('tbl_teeth_arrangements');
    }
}
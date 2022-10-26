<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForTheatreWaitingList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_theatre_waits', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');	
			$table->integer('confirm',false,true)->length(1);
			$table->integer('received',false,true)->length(1);
			$table->integer('nurse_id')->unsigned();
		    $table->foreign('nurse_id')->references('id')->on('users');
		    $table->date('posted_date');
		    $table->string('prescriptions',150)->nullable();
		    $table->string('operation_date',15)->nullable();		
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
        Schema::dropIfExists('tbl_theatre_waits');
    }
}
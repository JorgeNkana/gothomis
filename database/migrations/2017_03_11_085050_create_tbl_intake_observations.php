<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblIntakeObservations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_intake_observations', function (Blueprint $table) {
        $table->increments('id');
        
		$table->integer('admission_id')->unsigned();
        $table->foreign('admission_id')->references('id')->on('tbl_admissions');
        $table->integer('intravenous_types_id',false,true)->unsigned()->nullable(); 
		
		$table->foreign('intravenous_types_id')->references('id')->on('tbl_items');		
        $table->float('intravenous_mils',false,true)->unsigned()->nullable();
        $table->integer('oral_types_id',false,true)->unsigned()->nullable(); 
		
		$table->foreign('oral_types_id')->references('id')->on('tbl_items');	
        $table->float('oral_mils',false,true)->unsigned()->nullable();
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
        Schema::dropIfExists('tbl_intake_observations');
    }
}
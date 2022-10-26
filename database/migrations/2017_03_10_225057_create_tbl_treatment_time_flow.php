<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTreatmentTimeFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_treatment_time_flows', function (Blueprint $table) {
        $table->increments('id');
        
		$table->integer('treatment_charts_id')->unsigned();
		$table->foreign('treatment_charts_id')->references('id')->on('tbl_treatment_charts');
		$table->string('time_dosage',10)->nullable();
		$table->date('date_dosage')->nullable();
		$table->string('treatment_remarks',150)->nullable();			
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
        Schema::dropIfExists('tbl_treatment_time_flows');
    }
}
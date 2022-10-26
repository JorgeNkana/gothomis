<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTreatmentCharts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_treatment_charts', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');
			$table->integer('type_of_drugs_dosage_id',false,true)->unsigned(); 
		
			$table->foreign('type_of_drugs_dosage_id')->references('id')->on('tbl_items');
            $table->string('how_often',50);
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
        Schema::dropIfExists('tbl_treatment_charts');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblNewattendanceRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('tbl_newattendance_registers', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('facility_id')->unsigned();
			$table->Integer('clinic_id');
			$table->date('date');		
			$table->Integer('male_under_one_month')->default(0);		
			$table->Integer('female_under_one_month')->default(0);		
			$table->Integer('total_under_one_month')->default(0);		
			$table->Integer('male_under_one_year')->default(0);		
			$table->Integer('female_under_one_year')->default(0);		
			$table->Integer('total_under_one_year')->default(0);		
			$table->Integer('male_under_five_year')->default(0);		
			$table->Integer('female_under_five_year')->default(0);		
			$table->Integer('total_under_five_year')->default(0);		
			$table->Integer('male_above_five_under_sixty')->default(0);		
			$table->Integer('female_above_five_under_sixty')->default(0);		
			$table->Integer('total_above_five_under_sixty')->default(0);		
			$table->Integer('male_above_sixty')->default(0);		
			$table->Integer('female_above_sixty')->default(0);		
			$table->Integer('total_above_sixty')->default(0);		
			$table->Integer('total_male')->default(0);		
			$table->Integer('total_female')->default(0);		
			$table->Integer('grand_total')->default(0);		
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
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
        Schema::dropIfExists('tbl_newattendance_registers');
    }
}
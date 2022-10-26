<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDischargePermits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_discharge_permits', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');	
			$table->integer('confirm',false,true)->length(1);
			$table->integer('nurse_id')->unsigned();
		    $table->foreign('nurse_id')->references('id')->on('users');
		    $table->date('permission_date');
		    $table->string('domestic_dosage',150)->nullable();
		    $table->string('followup_date',15)->nullable();		
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
        Schema::dropIfExists('tbl_discharge_permits');
    }
}
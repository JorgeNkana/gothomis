<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForSurgeryFamillySocilalHsty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_surgery_family_socials', function (Blueprint $table) {
            $table->increments('id');
            
			$table->string('chronic_illness',80)->nullable();
            $table->string('substance_abuse',80)->nullable();
            $table->string('adoption',80)->nullable();
            $table->string('others',80)->nullable();
			$table->integer('erasor',false,true)->length(1)->unsigned();
			$table->integer('admission_id')->unsigned();
            $table->foreign('admission_id')->references('id')->on('tbl_admissions');	
			$table->integer('request_id')->unsigned();
            $table->foreign('request_id')->references('id')->on('tbl_theatre_waits');
			$table->string('other_information',150)->nullable();	
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
        Schema::dropIfExists('tbl_surgery_family_socials');
    }
}
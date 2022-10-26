<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForStaffSubsections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_staff_sections', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('section_id')->unsigned()->nullable();
		    $table->foreign('section_id')->references('id')->on('tbl_sub_departments');
            $table->integer('technologist_id')->unsigned();
		    $table->foreign('technologist_id')->references('id')->on('users');
			$table->string('isAllowed',6)->nullable();
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
        Schema::dropIfExists('tbl_staff_sections');
    }
}
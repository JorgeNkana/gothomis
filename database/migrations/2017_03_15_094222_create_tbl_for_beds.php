<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForBeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_beds', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('bed_name',50);
			$table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');$table->integer('ward_id')->unsigned();
            $table->foreign('ward_id')->references('id')->on('tbl_wards');		
			$table->integer('bed_type_id',false,true)->unsigned();
            $table->foreign('bed_type_id')->references('id')->on('tbl_bed_types');
			$table->integer('occupied',false,true)->length(1)->unsigned();			
			$table->integer('eraser',false,true)->length(1)->unsigned();			
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
        Schema::dropIfExists('tbl_beds');
    }
}
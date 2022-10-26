<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForWards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wards', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('ward_name',50);
            $table->integer('ward_type_id')->unsigned();
			$table->foreign('ward_type_id')->references('id')->on('tbl_wards_types');   
			$table->integer('ward_class_id',false,true)->unsigned(); 
			$table->foreign('ward_class_id')->references('id')->on('tbl_items');
            $table->integer('facility_id')->unsigned();
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
        Schema::dropIfExists('tbl_wards');
    }
}
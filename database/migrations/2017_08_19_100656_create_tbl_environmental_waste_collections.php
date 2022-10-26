<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEnvironmentalWasteCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_environmental_waste_collections', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('waste_type_id')->unsigned();
            $table->foreign('waste_type_id')->references('id')->on('tbl_waste_types');
            $table->integer('equipment_used_id')->unsigned();
            $table->foreign('equipment_used_id')->references('id')->on('tbl_environmental_equipment_registers');
            $table->integer('waste_collected',false,true);
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
        Schema::dropIfExists('tbl_environmental_waste_collections');
    }
}
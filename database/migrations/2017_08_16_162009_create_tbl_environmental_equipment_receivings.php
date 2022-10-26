<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEnvironmentalEquipmentReceivings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_environmental_equipment_receivings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('equipment_id')->unsigned();
            $table->foreign('equipment_id')->references('id')->on('tbl_environmental_equipment_registers');
            $table->integer('quantity',false,true);
            $table->string('status')->default('l');
            $table->string('status_received')->nullable();
            $table->integer('issued_quantity',false,true)->nullable();
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
        Schema::dropIfExists('tbl_environmental_equipment_receivings');
    }
}
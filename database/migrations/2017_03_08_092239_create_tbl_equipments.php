<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEquipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('tbl_equipments', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('equipment_name',30)->nullable();
            $table->string('reagents',100)->nullable();
            $table->integer('equipment_status_id',false,true)->unsigned()->nullable();
            $table->foreign('equipment_status_id')->references('id')->on('tbl_equipment_statuses');
			$table->string('description',100)->nullable();
			$table->integer('conditions',false,true)->length(1)->nullable();
			$table->integer('facility_id')->unsigned()->nullable();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
			$table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
			$table->integer('sub_department_id')->unsigned();
            $table->foreign('sub_department_id')->references('id')->on('tbl_sub_departments')->nullable();
			$table->boolean('eraser')->nullable();
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
        //
			Schema::dropIfExists('tbl_equipments');
    }
}
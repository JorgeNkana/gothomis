<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEquipmentStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		 Schema::create('tbl_equipment_statuses', function (Blueprint $table) {
            $table->increments('id');
			$table->string('status_name',85)->nullable();
			$table->integer('on_off',false,true)->length(1)->nullable();
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
		Schema::dropIfExists('tbl_equipment_statuses');
    }
}
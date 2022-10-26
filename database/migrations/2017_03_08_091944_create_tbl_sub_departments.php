<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSubDepartments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('tbl_sub_departments', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('sub_department_name',100);
            $table->integer('department_id',false,true)->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('tbl_departments');
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
		Schema::dropIfExists('tbl_sub_departments');
    }
}
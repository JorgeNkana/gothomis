<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_userDepartments', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('dept_id',false,true)->unsigned()->nullable();
           
            $table->foreign('dept_id')->references('id')->on('tbl_departments')->nullable();
            $table->integer('registered_by')->unsigned()->nullable();
            $table->foreign('registered_by')->references('id')->on('users');
           $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
			$table->integer('grant')->length(1)->unsigned()->nullable();
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
        Schema::dropIfExists('tbl_userDepartment');
    }
}
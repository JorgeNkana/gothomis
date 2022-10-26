<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_procedures',function (Blueprint $table){
            $table->increments('id');
            
            $table->string('procedure_name',100);
            $table->integer('dept_id',false,true)->unsigned()->nullable();
           
            $table->foreign('dept_id')->references('id')->on('tbl_departments');
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
        Schema::dropIfExist('tbl_procedures');
    }
}
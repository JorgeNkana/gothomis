<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblItemSubDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_item_sub_departments', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('sub_dept_id')->unsigned();
            $table->foreign('sub_dept_id')->references('id')->on('tbl_sub_departments');
            $table->integer('item_id',false,true)->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_items');
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
        Schema::dropIfExists('tbl_item_sub_departments');
    }
}
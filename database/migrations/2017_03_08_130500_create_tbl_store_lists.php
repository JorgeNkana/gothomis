<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStoreLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_store_lists', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('store_name',40);
            $table->integer('store_type_id',false,true)->nullable();
            $table->foreign('store_type_id')->references('id')->on('tbl_store_types');
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
        Schema::dropIfExists('tbl_store_lists');
    }
}
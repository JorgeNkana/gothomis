<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestigationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_investigation_details',function (Blueprint $table){
            $table->increments('id');
            
            $table->integer('investigation_id')->unsigned();
            $table->integer('test_id',false,true)->unsigned();
            $table->foreign('investigation_id')->references('id')->on('tbl_investigations');
            $table->foreign('test_id')->references('id')->on('tbl_items');
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
        Schema::dropIfExist('tbl_investigation_details');
    }
}
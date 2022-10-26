<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblWasteDispositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_waste_dispositions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('waste_type_id')->unsigned();
            $table->foreign('waste_type_id')->references('id')->on('tbl_waste_types');
            $table->integer('waste_disposal_type')->unsigned();
            $table->foreign('waste_disposal_type')->references('id')->on('tbl_waste_disposal_methods');
            $table->integer('waste_disposed',false,true);
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
        Schema::dropIfExists('tbl_waste_dispositions');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblItemTypeMapped extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_item_type_mappeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id',false,true)->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->string('item_code',45)->nullable();
            $table->string('item_category',35);
            $table->string('Dose_formulation',25)->nullable();
            $table->string('sub_item_category',60)->nullable();
            $table->integer('strength',false,true)->length(8)->nullable();
            $table->integer('volume',false,true)->length(8)->nullable();
            $table->integer('unit_of_measure',false,true)->length(8)->unsigned()->nullable();
            $table->string('dispensing_unit',35)->nullable();
            $table->string('IsRestricted',7)->nullable();


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
        Schema::dropIfExists('tbl_item_type_mapped');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductRegistriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_registries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_code')->nullable();
            $table->text('item_name');
            $table->text('item_category')->nullable();
            $table->text('item_sub_category')->nullable();
            $table->text('unit_of_measure');
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
        Schema::dropIfExists('tbl_product_registries');
    }
}
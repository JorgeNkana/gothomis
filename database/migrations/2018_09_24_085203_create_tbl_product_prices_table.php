<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_product_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_code')->nullable();
            $table->text('item_name');
            $table->double('item_price');
            $table->boolean('status')->default(1);
            $table->integer('item_id',false,true);
             $table->foreign('item_id')->references('id')->on('tbl_product_registries')->onupdate('cascade');
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
        Schema::dropIfExists('tbl_product_prices');
    }
}
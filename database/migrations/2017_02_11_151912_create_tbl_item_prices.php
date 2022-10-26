<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblItemPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_item_prices', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('item_id',false,true)->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->double('price',false,true);
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('exemption_status',false,true)->default(0);
            $table->integer('onetime',false,true)->default(0);
            $table->integer('insurance',false,true)->default(1);
	$table->integer('status',false,true)->default(1);
	    $table->integer('sub_category_id',false,true)->unsigned();
            $table->foreign('sub_category_id')->references('id')->on('tbl_pay_cat_sub_categories');
            $table->date('startingFinancialYear');
            $table->date('endingFinancialYear');
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
        Schema::dropIfExists('tbl_item_prices');
    }
}
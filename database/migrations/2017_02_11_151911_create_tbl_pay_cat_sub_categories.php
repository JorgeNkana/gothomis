<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPayCatSubCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pay_cat_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sub_category_name',50);
            $table->integer('pay_cat_id',false,true)->unsigned();
            $table->foreign('pay_cat_id')->references('id')->on('tbl_payments_categories');
            $table->integer('facility_id')->unsigned()->nullable();
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
        Schema::dropIfExists('tbl_pay_cat_sub_categories');
    }
}
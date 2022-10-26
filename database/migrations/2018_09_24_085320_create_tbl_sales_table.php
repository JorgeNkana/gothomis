<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->text('item_name');
            $table->double('unit_price');
            $table->double('quantity');
            $table->date('expiry_date');
            $table->string('invoice_number');
            $table->string('batch_number');
            $table->string('payment_status')->default('UNPAID');
            $table->string('buyer_name');
            $table->string('seller_name');
            $table->integer('user_id',false,true);
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
        Schema::dropIfExists('tbl_sales');
    }
}
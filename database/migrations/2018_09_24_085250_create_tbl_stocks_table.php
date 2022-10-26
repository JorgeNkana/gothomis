<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('item_name');
            $table->text('vendor_name');
            $table->date('expiry_date');
            $table->double('unit_price');
            $table->double('quantity');
            $table->double('balance');
            $table->string('user_name');
            $table->double('useless')->nullable();
            $table->double('pending_balance')->nullable();
            $table->string('useless_reason')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('control_in')->nullable();
            $table->string('control_out')->nullable();
            $table->integer('user_id',false,true)->nullable();
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
        Schema::dropIfExists('tbl_stocks');
    }
}
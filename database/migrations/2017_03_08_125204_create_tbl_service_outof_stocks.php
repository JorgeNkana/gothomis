<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblServiceOutofStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('tbl_service_outof_stock', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('item',30);
            $table->string('description',100);
            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('tbl_requests');
            $table->integer('test_id')->unsigned()->nullable();
            $table->foreign('test_id')->references('id')->on('tbl_tests');
			$table->boolean('eraser')->nullable();
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
        //
		Schema::dropIfExists('tbl_service_outof_stock');
    }
}
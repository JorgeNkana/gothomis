<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCashDeposits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cash_deposits', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('transaction',250);
            $table->string('BillId',250);
            $table->double('amount');
            $table->double('AmountPaid')->nullable();
            $table->string('PspReceiptNumber',250)->nullable();
            $table->integer('facility_id')->unsigned();
            $table->boolean('cancelled')->default(false);
            $table->integer('user_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities')->onupdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onupdate('cascade');
            $table->timestamps();
            $table->timestamp("paid_at")>nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_cash_deposits');
    }
}
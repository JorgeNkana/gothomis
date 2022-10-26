<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_number');
            $table->double('cost_amount');
            $table->string('payment_status')->default('UNPAID');
            $table->text('payer_name')->nullable();
            $table->text('payment_agent_name')->nullable();
            $table->string('payslip')->nullable();
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
        Schema::dropIfExists('tbl_payments');
    }
}
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_invoices', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('invoice_number',10);
            $table->integer('vendor_id')->unsigned();
            $table->foreign('vendor_id')->references('id')->on('tbl_vendors');
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
        Schema::dropIfExists('tbl_invoices');
    }
}
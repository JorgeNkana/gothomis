<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEncounterInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_encounter_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('BillId')->nullable();            
            $table->integer('account_number_id')->unsigned()->nullable();
            $table->foreign('account_number_id')->references('id')->on('tbl_accounts_numbers');

			$table->integer('corpse_id')->unsigned()->nullable();
            $table->foreign('corpse_id')->references('id')->on('tbl_corpses');			
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
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
        Schema::dropIfExists('tbl_encounter_invoices');
    }
}
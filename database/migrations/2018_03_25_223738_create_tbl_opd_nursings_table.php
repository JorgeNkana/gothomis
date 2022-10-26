<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblOpdNursingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_opd_nursings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('visit_id')->unsigned();
            $table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('item_id')->unsigned();
            $table->foreign('item_id')->references('id')->on('tbl_items');
            $table->string('service_type')->nullable();
            $table->string('periodic')->nullable();
$table->string('route')->nullable();
$table->string('duration')->nullable();

            $table->integer('status',false,true)->default(0);
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
        Schema::dropIfExists('tbl_opd_nursings');
    }
}